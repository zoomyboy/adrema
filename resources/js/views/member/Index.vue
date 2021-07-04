<template>
    <div>

        <div class="custom-table">
            <header>
                <div>Nachname</div>
                <div>Vorname</div>
                <div>Straße</div>
                <div>PLZ</div>
                <div>Ort</div>
                <div>Mittendrin</div>
                <div>Nami</div>
                <div>Check</div>
                <div>Rechnung</div>
                <div>Geburtstag</div>
                <div>Eintritt</div>
                <div></div>
            </header>

            <div v-for="member, index in data.data">
                <div v-text="member.firstname"></div>
                <div v-text="member.lastname"></div>
                <div v-text="`${member.address}`"></div>
                <div v-text="`${member.zip}`"></div>
                <div v-text="`${member.location}`"></div>
                <div>
                    <v-bool v-model="member.send_newspaper"></v-bool>
                </div>
                <div>
                    <v-bool v-model="member.has_nami"></v-bool>
                </div>
                <div>
                    <v-bool v-model="member.is_confirmed"></v-bool>
                </div>
                <div>
                    <div class="py-1 rounded-full flex text-xs items-center justify-center leading-none bg-primary-900" v-text="member.bill_kind_name" v-if="member.bill_kind_name"></div>
                    <div class="py-1 rounded-full flex text-xs items-center justify-center leading-none" v-else>Kein</div>
                </div>
                <div v-text="`${member.birthday_human}`"></div>
                <div v-text="`${member.joined_at_human}`"></div>
                <div class="flex">
                    <inertia-link :href="`/member/${member.id}/edit`" class="inline-flex btn btn-warning btn-sm"><sprite src="pencil"></sprite></inertia-link>
                    <inertia-link :href="`/member/${member.id}/payment`" class="inline-flex btn btn-info btn-sm"><sprite src="money"></sprite></inertia-link>
                </div>
            </div>

        </div>

        <div class="px-6">
            <pages class="mt-4" :value="data.meta" :only="['data']"></pages>
        </div>

        <transition name="sidebar">
            <payments v-if="single !== null && single.mode === 'index'" v-model="single"></payments>
            <payment-form v-if="single !== null && single.mode === 'create'" v-model="single"></payment-form>
            <payment-form v-if="single !== null && single.mode === 'edit'" v-model="single"></payment-form>
        </transition>
    </div>
</template>

<script>
import App from '../../layouts/App';
import Payments from './Payments.vue';
import PaymentForm from './PaymentForm.vue';

export default {
    layout: App,

    components: { Payments, PaymentForm },

    props:{
        data: {},
        single: {
            default: function() { return null; }
        },
    }
}
</script>

