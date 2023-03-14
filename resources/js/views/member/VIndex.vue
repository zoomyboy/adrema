<template>
    <div class="pb-6">
        <div class="px-6 py-2 flex border-b border-gray-600 items-center space-x-3">
            <f-switch v-show="hasModule('bill')" id="ausstand" @input="setFilter('ausstand', $event)" :items="getFilter('ausstand')" label="Nur Ausstände" size="sm"></f-switch>
            <f-select
                v-show="hasModule('bill')"
                name="billKinds"
                id="billKinds"
                @input="setFilter('bill_kind', $event)"
                :options="billKinds"
                :value="getFilter('bill_kind')"
                label="Rechnung"
                size="sm"
            ></f-select>
            <f-select
                id="activity_id"
                @input="setFilter('activity_id', $event)"
                :options="filterActivities"
                :value="getFilter('activity_id')"
                label="Tätigkeit"
                size="sm"
                name="activity_id"
            ></f-select>
            <f-select
                id="subactivity_id"
                @input="setFilter('subactivity_id', $event)"
                :options="filterSubactivities"
                :value="getFilter('subactivity_id')"
                label="Untertätigkeit"
                size="sm"
                name="subactivity_id"
            ></f-select>
            <button class="btn btn-primary label mr-2" @click.prevent="exportMembers">
                <svg-sprite class="w-3 h-3 xl:mr-2" src="save"></svg-sprite>
                <span class="hidden xl:inline">Exportieren</span>
            </button>
        </div>

        <table cellspacing="0" cellpadding="0" border="0" class="custom-table custom-table-sm hidden md:table">
            <thead>
                <th></th>
                <th>Nachname</th>
                <th>Vorname</th>
                <th class="hidden 2xl:table-cell">Ort</th>
                <th>Tags</th>
                <th class="hidden xl:table-cell">Alter</th>
                <th class="hidden xl:table-cell" v-show="hasModule('bill')">Rechnung</th>
                <th v-show="hasModule('bill')">Ausstand</th>
                <th></th>
            </thead>

            <tr v-for="(member, index) in inner.data" :key="index">
                <td><age-groups :member="member"></age-groups></td>
                <td v-text="member.lastname"></td>
                <td v-text="member.firstname"></td>
                <td class="hidden 2xl:table-cell" v-text="member.full_address"></td>
                <td><tags :member="member"></tags></td>
                <td class="hidden xl:table-cell" v-text="member.age"></td>
                <td class="hidden xl:table-cell" v-show="hasModule('bill')">
                    <v-label :value="member.bill_kind_name" fallback="kein"></v-label>
                </td>
                <td v-show="hasModule('bill')">
                    <v-label :value="member.pending_payment" fallback="---"></v-label>
                </td>
                <td>
                    <actions :member="member" @sidebar="openSidebar(index, $event)" @remove="remove(member)"></actions>
                </td>
            </tr>
        </table>

        <div class="md:hidden p-3 grid gap-3">
            <box class="relative" :heading="member.fullname" v-for="(member, index) in data.data" :key="index">
                <div slot="in-title">
                    <age-groups class="ml-2" :member="member" icon-class="w-4 h-4"></age-groups>
                </div>
                <div class="text-xs text-gray-200" v-text="member.full_address"></div>
                <div class="flex items-center mt-1 space-x-4">
                    <tags :member="member"></tags>
                    <v-label class="text-gray-100 block" v-show="hasModule('bill')" :value="member.pending_payment" fallback=""></v-label>
                </div>
                <actions class="mt-2" :member="member" @sidebar="openSidebar(index, $event)" @remove="remove(member)"></actions>
                <div class="absolute right-0 top-0 h-full flex items-center mr-2">
                    <i-link :href="member.links.show" v-tooltip="`Details`"><svg-sprite src="chevron-down" class="w-6 h-6 text-teal-100 -rotate-90"></svg-sprite></i-link>
                </div>
            </box>
        </div>

        <div class="px-6">
            <v-pages class="mt-4" :value="data.meta" :only="['data']"></v-pages>
        </div>

        <transition name="sidebar">
            <member-payments
                v-if="single !== null && sidebar === 'payment.index'"
                @close="closeSidebar"
                :subscriptions="subscriptions"
                :statuses="statuses"
                :value="data.data[single]"
            ></member-payments>
            <member-memberships
                v-if="single !== null && sidebar === 'membership.index'"
                @close="closeSidebar"
                :groups="data.meta.groups"
                :activities="activities"
                :subactivities="subactivities"
                :value="data.data[single]"
            ></member-memberships>
            <member-courses v-if="single !== null && sidebar === 'courses.index'" @close="closeSidebar" :courses="courses" :value="data.data[single]"></member-courses>
        </transition>
    </div>
</template>

<script>
import MemberPayments from './MemberPayments.vue';
import MemberMemberships from './MemberMemberships.vue';
import MemberCourses from './MemberCourses.vue';
import indexHelpers from '../../mixins/indexHelpers.js';

export default {
    data: function () {
        return {
            sidebar: null,
            single: null,
        };
    },

    mixins: [indexHelpers],

    components: {
        MemberMemberships,
        MemberPayments,
        MemberCourses,
        'age-groups': () => import(/* webpackChunkName: "member" */ './AgeGroups'),
        'tags': () => import(/* webpackChunkName: "member" */ './Tags'),
        'actions': () => import(/* webpackChunkName: "member" */ './index/Actions'),
    },

    methods: {
        exportMembers() {
            window.open(`/member-export?filter=${this.filterString}`);
        },
        remove(member) {
            if (window.confirm('Mitglied löschen?')) {
                this.$inertia.delete(`/member/${member.id}`);
            }
        },
        openSidebar(index, name) {
            this.single = index;
            this.sidebar = name;
        },
        closeSidebar() {
            this.single = null;
            this.sidebar = null;
        },
    },

    props: {
        subscriptions: {},
        statuses: {},
        paymentDefaults: {},
        query: {},
        billKinds: {},
        activities: {},
        subactivities: {},
        filterActivities: {},
        filterSubactivities: {},
        courses: {},
    },
};
</script>
