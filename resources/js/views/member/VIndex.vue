<template>
    <div>
        <member-filter
            :value="query.filter"
            :activities="filterActivities"
            :subactivities="filterSubactivities"
            :bill-kinds="billKinds"
        ></member-filter>

        <table cellspacing="0" cellpadding="0" border="0" class="custom-table custom-table-sm">
            <thead>
                <th></th>
                <th>Nachname</th>
                <th>Vorname</th>
                <th>Straße</th>
                <th>PLZ</th>
                <th>Ort</th>
                <th>Tags</th>
                <th>Beitrag</th>
                <th>Geburtstag</th>
                <th v-show="hasModule('bill')">Rechnung</th>
                <th v-show="hasModule('bill')">Ausstand</th>
                <th>Eintritt</th>
                <th></th>
            </thead>

            <tr v-for="(member, index) in data.data" :key="index">
                <td class="w-6 flex gap-1 items-center">
                    <svg-sprite
                        class="w-3 h-3 flex-none"
                        v-if="member.is_leader"
                        :class="ageColors.leiter"
                        src="lilie"
                    ></svg-sprite>
                    <svg-sprite
                        class="w-3 h-3 flex-none"
                        v-if="member.age_group_icon"
                        :class="ageColors[member.age_group_icon]"
                        src="lilie"
                    ></svg-sprite>
                </td>
                <td v-text="member.lastname"></td>
                <td v-text="member.firstname"></td>
                <td v-text="`${member.address}`"></td>
                <td v-text="`${member.zip}`"></td>
                <td v-text="`${member.location}`"></td>
                <td>
                    <div class="bool-row">
                        <v-bool
                            true-comment="Mittendrin abonniert"
                            false-comment="Mittendrin nicht abonníert"
                            v-model="member.send_newspaper"
                            >M</v-bool
                        >
                        <v-bool
                            true-comment="In NaMi eingetragen"
                            false-comment="Nicht in NaMi eingetragen"
                            v-model="member.has_nami"
                            >N</v-bool
                        >
                        <v-bool
                            true-comment="Daten bestätigt"
                            false-comment="Daten warten auf Bestätigung"
                            v-model="member.is_confirmed"
                            >C</v-bool
                        >
                    </div>
                </td>
                <td v-text="member.subscription ? member.subscription.name : ''"></td>
                <td v-text="`${member.birthday_human} (${member.age})`"></td>
                <td v-show="hasModule('bill')">
                    <div class="flex justify-center">
                        <div
                            class="btn btn-sm label primary"
                            v-text="member.bill_kind_name"
                            v-if="member.bill_kind_name"
                        ></div>
                        <div class="text-xs" v-else>Kein</div>
                    </div>
                </td>
                <td v-show="hasModule('bill')">
                    <div class="flex justify-center">
                        <div
                            class="btn btn-sm label primary"
                            v-show="member.pending_payment"
                            v-text="member.pending_payment"
                        ></div>
                    </div>
                </td>
                <td v-text="`${member.joined_at_human}`"></td>
                <td>
                    <div class="flex space-x-1">
                        <i-link :href="member.links.show" class="inline-flex btn btn-primary btn-sm"
                            ><svg-sprite src="eye"></svg-sprite
                        ></i-link>
                        <i-link :href="`/member/${member.id}/edit`" class="inline-flex btn btn-warning btn-sm"
                            ><svg-sprite src="pencil"></svg-sprite
                        ></i-link>
                        <a
                            href="#"
                            v-show="hasModule('bill')"
                            @click.prevent="openSidebar(index, 'payment.index')"
                            class="inline-flex btn btn-info btn-sm"
                            ><svg-sprite src="money"></svg-sprite
                        ></a>
                        <a
                            href="#"
                            v-show="hasModule('courses')"
                            @click.prevent="openSidebar(index, 'courses.index')"
                            class="inline-flex btn btn-info btn-sm"
                            ><svg-sprite src="course"></svg-sprite
                        ></a>
                        <a
                            href="#"
                            @click.prevent="openSidebar(index, 'membership.index')"
                            class="inline-flex btn btn-info btn-sm"
                            ><svg-sprite src="user"></svg-sprite
                        ></a>
                        <a :href="member.efz_link" v-show="member.efz_link" class="inline-flex btn btn-info btn-sm"
                            ><svg-sprite src="report"></svg-sprite
                        ></a>
                        <i-link href="#" @click.prevent="remove(member)" class="inline-flex btn btn-danger btn-sm"
                            ><svg-sprite src="trash"></svg-sprite
                        ></i-link>
                    </div>
                </td>
            </tr>
        </table>

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
            ageColors: {
                biber: 'text-biber',
                woelfling: 'text-woelfling',
                jungpfadfinder: 'text-jungpfadfinder',
                pfadfinder: 'text-pfadfinder',
                rover: 'text-rover',
                leiter: 'text-leiter',
            },
        };
    },

    mixins: [mergesQueryString],

    components: {MemberMemberships, MemberPayments, MemberFilter, MemberCourses},

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
