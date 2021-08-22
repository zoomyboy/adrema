<template>
    <div>

        <filt v-model="query.filter" :bill-kinds="billKinds"></filt>

        <div class="custom-table">
            <header>
                <div>Nachname</div>
                <div>Vorname</div>
                <div>Straße</div>
                <div>PLZ</div>
                <div>Ort</div>
                <div>Tags</div>
                <div>Beitrag</div>
                <div>Geburtstag</div>
                <div>Rechnung</div>
                <div>Ausstand</div>
                <div>Eintritt</div>
                <div></div>
            </header>

            <div v-for="member, index in data.data">
                <div v-text="member.lastname"></div>
                <div v-text="member.firstname"></div>
                <div v-text="`${member.address}`"></div>
                <div v-text="`${member.zip}`"></div>
                <div v-text="`${member.location}`"></div>
                <div>
                    <div class="bool-row">
                        <v-bool v-model="member.send_newspaper">M</v-bool>
                        <v-bool v-model="member.has_nami">N</v-bool>
                        <v-bool v-model="member.is_confirmed">C</v-bool>
                    </div>
                </div>
                <div v-text="member.subscription_name"></div>
                <div v-text="`${member.birthday_human}`"></div>
                <div>
                    <div class="flex justify-center">
                        <div class="btn btn-sm label primary" v-text="member.bill_kind_name" v-if="member.bill_kind_name"></div>
                        <div class="text-xs" v-else>Kein</div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-center">
                        <div class="btn btn-sm label primary" v-show="member.pending_payment" v-text="member.pending_payment"></div>
                    </div>
                </div>
                <div v-text="`${member.joined_at_human}`"></div>
                <div class="flex">
                    <inertia-link :href="`/member/${member.id}/edit`" class="inline-flex btn btn-warning btn-sm"><sprite src="pencil"></sprite></inertia-link>
                    <a href="#" @click.prevent="openSidebar(index, 'payment.index')" class="inline-flex btn btn-info btn-sm"><sprite src="money"></sprite></a>
                    <inertia-link href="#" @click.prevent="remove(member)" class="inline-flex btn btn-danger btn-sm"><sprite src="trash"></sprite></inertia-link>
                </div>
            </div>

        </div>

        <div class="px-6">
            <pages class="mt-4" :value="data.meta" :only="['data']"></pages>
        </div>

        <transition name="sidebar">
            <payments v-if="single !== null && sidebar === 'payment.index'" @close="closeSidebar" :subscriptions="subscriptions" :statuses="statuses" v-model="data.data[single]"></payments>
        </transition>
    </div>
</template>

<script>
import App from '../../layouts/App';
import Payments from './Payments.vue';
import Filt from './Filt.vue';
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

    components: { Payments, Filt },

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
    }
}
</script>

