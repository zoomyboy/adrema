<template>
    <div>

        <member-filter :value="query.filter" :activities="filterActivities" :subactivities="filterSubactivities" :bill-kinds="billKinds"></member-filter>

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

            <tr v-for="member, index in data.data" :key="index">
                <td class="w-3">
                    <sprite class="w-3 h-3" v-if="member.age_group_icon" :class="`text-${member.age_group_icon}`" src="lilie"></sprite>
                </td>
                <td v-text="member.lastname"></td>
                <td v-text="member.firstname"></td>
                <td v-text="`${member.address}`"></td>
                <td v-text="`${member.zip}`"></td>
                <td v-text="`${member.location}`"></td>
                <td>
                    <div class="bool-row">
                        <v-bool v-model="member.send_newspaper">M</v-bool>
                        <v-bool v-model="member.has_nami">N</v-bool>
                        <v-bool v-model="member.is_confirmed">C</v-bool>
                    </div>
                </td>
                <td v-text="member.subscription_name"></td>
                <td v-text="`${member.birthday_human}`"></td>
                <td v-show="hasModule('bill')">
                    <div class="flex justify-center">
                        <div class="btn btn-sm label primary" v-text="member.bill_kind_name" v-if="member.bill_kind_name"></div>
                        <div class="text-xs" v-else>Kein</div>
                    </div>
                </td>
                <td v-show="hasModule('bill')">
                    <div class="flex justify-center">
                        <div class="btn btn-sm label primary" v-show="member.pending_payment" v-text="member.pending_payment"></div>
                    </div>
                </td>
                <td v-text="`${member.joined_at_human}`"></td>
                <td class="flex">
                    <Link :href="`/member/${member.id}/edit`" class="inline-flex btn btn-warning btn-sm"><sprite src="pencil"></sprite></Link>
                    <a href="#" v-show="hasModule('bill')" @click.prevent="openSidebar(index, 'payment.index')" class="inline-flex btn btn-info btn-sm"><sprite src="money"></sprite></a>
                    <a href="#" v-show="hasModule('courses')" @click.prevent="openSidebar(index, 'courses.index')" class="inline-flex btn btn-info btn-sm"><sprite src="course"></sprite></a>
                    <a href="#" @click.prevent="openSidebar(index, 'membership.index')" class="inline-flex btn btn-info btn-sm"><sprite src="user"></sprite></a>
                    <Link href="#" @click.prevent="remove(member)" class="inline-flex btn btn-danger btn-sm"><sprite src="trash"></sprite></Link>
                </td>
            </tr>

        </table>

        <div class="px-6">
            <pages class="mt-4" :value="data.meta" :only="['data']"></pages>
        </div>

        <transition name="sidebar">
            <member-payments v-if="single !== null && sidebar === 'payment.index'" @close="closeSidebar" :subscriptions="subscriptions" :statuses="statuses" :value="data.data[single]"></member-payments>
            <member-memberships v-if="single !== null && sidebar === 'membership.index'" @close="closeSidebar" :activities="activities" :subactivities="subactivities" :value="data.data[single]"></member-memberships>
            <member-courses v-if="single !== null && sidebar === 'courses.index'" @close="closeSidebar" :courses="courses" :value="data.data[single]"></member-courses>
        </transition>
    </div>
</template>

<script>
import App from '../../layouts/App';
import MemberPayments from './MemberPayments.vue';
import MemberMemberships from './MemberMemberships.vue';
import MemberCourses from './MemberCourses.vue';
import MemberFilter from './MemberFilter.vue';
import mergesQueryString from '../../mixins/mergesQueryString.js';

export default {

    data: function() {
        return {
            sidebar: null,
            single: null,
        };
    },

    layout: App,

    mixins: [mergesQueryString],

    components: { MemberMemberships, MemberPayments, MemberFilter, MemberCourses },

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
        }
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
    }
}
</script>

