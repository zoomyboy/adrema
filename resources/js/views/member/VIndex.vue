<template>
    <div class="pb-6">
        <member-filter
            :value="query.filter"
            :activities="filterActivities"
            :subactivities="filterSubactivities"
            :bill-kinds="billKinds"
        ></member-filter>

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

            <tr v-for="(member, index) in data.data" :key="index">
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
                    <div class="flex space-x-1">
                        <i-link
                            :href="member.links.show"
                            class="inline-flex btn btn-primary btn-sm"
                            v-tooltip="`Details`"
                            ><svg-sprite src="eye"></svg-sprite
                        ></i-link>
                        <i-link
                            :href="`/member/${member.id}/edit`"
                            class="inline-flex btn btn-warning btn-sm"
                            v-tooltip="`bearbeiten`"
                            ><svg-sprite src="pencil"></svg-sprite
                        ></i-link>
                        <a
                            href="#"
                            v-tooltip="`Zahlungen`"
                            v-show="hasModule('bill')"
                            @click.prevent="openSidebar(index, 'payment.index')"
                            class="inline-flex btn btn-info btn-sm"
                            ><svg-sprite src="money"></svg-sprite
                        ></a>
                        <a
                            href="#"
                            v-tooltip="`Ausbildungen`"
                            v-show="hasModule('courses')"
                            @click.prevent="openSidebar(index, 'courses.index')"
                            class="inline-flex btn btn-info btn-sm"
                            ><svg-sprite src="course"></svg-sprite
                        ></a>
                        <a
                            href="#"
                            v-tooltip="`Mitgliedschaften`"
                            @click.prevent="openSidebar(index, 'membership.index')"
                            class="inline-flex btn btn-info btn-sm"
                            ><svg-sprite src="user"></svg-sprite
                        ></a>
                        <a
                            :href="member.efz_link"
                            v-show="member.efz_link"
                            class="inline-flex btn btn-info btn-sm"
                            v-tooltip="`EFZ Formular`"
                            ><svg-sprite src="report"></svg-sprite
                        ></a>
                        <i-link
                            href="#"
                            @click.prevent="remove(member)"
                            class="inline-flex btn btn-danger btn-sm"
                            v-tooltip="`Entfernen`"
                            ><svg-sprite src="trash"></svg-sprite
                        ></i-link>
                    </div>
                </td>
            </tr>
        </table>

        <div class="md:hidden p-3 grid gap-3">
            <box class="relative" :heading="member.fullname" v-for="(member, index) in data.data" :key="index">
                <div slot="in-title">
                    <age-groups class="ml-2" :member="member" icon-class="w-4 h-4"></age-groups>
                </div>
                <div class="text-xs text-gray-200" v-text="member.full_address"></div>
                <div class="flex items-center mt-1 space-x-2">
                    <tags :member="member"></tags>
                    <v-label
                        class="text-gray-100"
                        v-show="hasModule('bill')"
                        :value="member.pending_payment"
                        fallback=""
                    ></v-label>
                </div>
                <div class="absolute right-0 top-0 h-full flex items-center mr-2">
                    <i-link :href="member.links.show" v-tooltip="`Details`"
                        ><svg-sprite src="chevron-down" class="w-6 h-6 text-teal-100 -rotate-90"></svg-sprite
                    ></i-link>
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
                :activities="activities"
                :subactivities="subactivities"
                :value="data.data[single]"
            ></member-memberships>
            <member-courses
                v-if="single !== null && sidebar === 'courses.index'"
                @close="closeSidebar"
                :courses="courses"
                :value="data.data[single]"
            ></member-courses>
        </transition>
    </div>
</template>

<script>
import MemberPayments from './MemberPayments.vue';
import MemberMemberships from './MemberMemberships.vue';
import MemberCourses from './MemberCourses.vue';
import MemberFilter from './MemberFilter.vue';
import mergesQueryString from '../../mixins/mergesQueryString.js';

export default {
    data: function () {
        return {
            sidebar: null,
            single: null,
        };
    },

    mixins: [mergesQueryString],

    components: {
        MemberMemberships,
        MemberPayments,
        MemberFilter,
        MemberCourses,
        'age-groups': () => import(/* webpackChunkName: "member" */ './AgeGroups'),
        'tags': () => import(/* webpackChunkName: "member" */ './Tags'),
        'box': () => import(/* webpackChunkName: "member" */ './Box'),
    },

    methods: {
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
        data: {},
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
