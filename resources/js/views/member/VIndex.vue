<template>
    <page-layout page-class="pb-6">
        <div class="flex" slot="toolbar">
            <toolbar-button :href="data.meta.links.create" color="primary" icon="plus">Mitglied anlegen</toolbar-button>
            <toolbar-button :href="data.meta.links.allpayment" color="primary" icon="invoice" v-if="hasModule('bill')">Rechnungen erstellen</toolbar-button>
            <toolbar-button :href="data.meta.links.sendpayment" color="info" icon="envelope" v-if="hasModule('bill')">Rechnungen versenden</toolbar-button>
        </div>
        <popup heading="Mitglied löschen?" v-if="deleting !== null" @close="deleting.reject()">
            <div>
                <p class="mt-4">Das Mitglied "{{ deleting.member.fullname }}" löschen?</p>
                <p class="mt-2">Alle Zuordnungen (Ausbildungen, Rechnungen, Zahlungen, Tätigkeiten) werden ebenfalls entfernt.</p>
                <note class="mt-5" type="warning" v-if="!deleting.member.has_nami"> Dieses Mitglied ist nicht in NaMi vorhanden und wird daher nur in der AdReMa gelöscht werden. </note>
                <note class="mt-5" type="danger" v-if="deleting.member.has_nami">
                    Dieses Mitglied ist in NaMi vorhanden und wird daher in NaMi abgemeldet werden. Sofern "Datenweiterverwendung" eingeschaltet ist, wird das Mitglied auf inaktiv gesetzt.
                </note>
                <div class="grid grid-cols-2 gap-3 mt-6">
                    <a href="#" @click.prevent="deleting.resolve()" class="text-center btn btn-danger">Mitglied loschen</a>
                    <a href="#" @click.prevent="deleting.reject()" class="text-center btn btn-primary">Abbrechen</a>
                </div>
            </div>
        </popup>
        <div class="px-6 py-2 flex border-b border-gray-600 items-center space-x-3">
            <f-text :value="getFilter('search')" @input="setFilter('search', $event)" id="search" name="search" label="Suchen …" size="sm"></f-text>
            <f-switch v-show="hasModule('bill')" id="ausstand" @input="setFilter('ausstand', $event)" :items="getFilter('ausstand')" label="Nur Ausstände" size="sm"></f-switch>
            <f-select id="group_id" @input="setFilter('group_id', $event)" :options="data.meta.groups" :value="getFilter('group_id')" label="Gruppierung" size="sm" name="group_id"></f-select>
            <f-select
                v-show="hasModule('bill')"
                name="billKinds"
                id="billKinds"
                @input="setFilter('bill_kind', $event)"
                :options="data.meta.billKinds"
                :value="getFilter('bill_kind')"
                label="Rechnung"
                size="sm"
            ></f-select>
            <f-select
                id="activity_id"
                @input="setFilter('activity_id', $event)"
                :options="data.meta.filterActivities"
                :value="getFilter('activity_id')"
                label="Tätigkeit"
                size="sm"
                name="activity_id"
            ></f-select>
            <f-select
                id="subactivity_id"
                @input="setFilter('subactivity_id', $event)"
                :options="data.meta.filterSubactivities"
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
                :subscriptions="data.meta.subscriptions"
                :statuses="data.meta.statuses"
                :value="data.data[single]"
            ></member-payments>
            <member-memberships
                v-if="single !== null && sidebar === 'membership.index'"
                @close="closeSidebar"
                :groups="data.meta.groups"
                :activities="data.meta.formActivities"
                :subactivities="data.meta.formSubactivities"
                :value="data.data[single]"
            ></member-memberships>
            <member-courses v-if="single !== null && sidebar === 'courses.index'" @close="closeSidebar" :courses="data.meta.courses" :value="data.data[single]"></member-courses>
        </transition>
    </page-layout>
</template>

<script>
import MemberPayments from './MemberPayments.vue';
import MemberMemberships from './MemberMemberships.vue';
import MemberCourses from './MemberCourses.vue';
import indexHelpers from '../../mixins/indexHelpers.js';
import hasModule from '../../mixins/hasModule.js';

export default {
    data: function () {
        return {
            sidebar: null,
            single: null,
            deleting: null,
        };
    },

    mixins: [indexHelpers, hasModule],

    components: {
        MemberMemberships,
        MemberPayments,
        MemberCourses,
        'age-groups': () => import(/* webpackChunkName: "member" */ './AgeGroups'),
        'tags': () => import(/* webpackChunkName: "member" */ './Tags'),
        'actions': () => import(/* webpackChunkName: "member" */ './index/Actions'),
        'popup': () => import(/* webpackChunkName: "ui" */ '../../components/ui/Popup.vue'),
        'note': () => import(/* webpackChunkName: "ui" */ '../../components/ui/Note.vue'),
    },

    methods: {
        exportMembers() {
            window.open(`/member-export?filter=${this.filterString}`);
        },
        async remove(member) {
            new Promise((resolve, reject) => {
                this.deleting = {resolve, reject, member};
            })
                .then(() => {
                    this.$inertia.delete(`/member/${member.id}`);
                    this.deleting = null;
                })
                .catch(() => {
                    this.deleting = null;
                });
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
        query: {},
    },
};
</script>
