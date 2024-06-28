<template>
    <page-layout>
        <template #right>
            <f-save-button form="billsettingform"></f-save-button>
        </template>
        <setting-layout>
            <form id="billsettingform" class="grow p-6 grid grid-cols-2 gap-3 items-start content-start" @submit.prevent="submit">
                <f-text id="from" v-model="inner.from" label="Absender" hint="Absender-Name in Kurzform, i.d.R. der kurze Stammesname"></f-text>
                <f-text id="from_long" v-model="inner.from_long" label="Absender (lang)" hint="Absender-Name in Langform, i.d.R. der Stammesname"></f-text>
                <h2 class="text-lg font-semibold text-gray-300 col-span-2 mt-5">Kontaktdaten</h2>
                <div class="col-span-2 text-gray-300 text-sm">Diese Kontaktdaten stehen im Absender-Bereich auf der Rechnung.</div>
                <f-text id="address" v-model="inner.address" label="Straße"></f-text>
                <f-text id="zip" v-model="inner.zip" label="PLZ"></f-text>
                <f-text id="place" v-model="inner.place" label="Ort"></f-text>
                <f-text id="email" v-model="inner.email" label="E-Mail-Adresse"></f-text>
                <f-text id="mobile" v-model="inner.mobile" label="Telefonnummer"></f-text>
                <f-text id="website" v-model="inner.website" label="Webseite"></f-text>
                <f-text id="iban" v-model="inner.iban" label="IBAN"></f-text>
                <f-text id="bic" v-model="inner.bic" label="BIC"></f-text>
            </form>
        </setting-layout>
    </page-layout>
</template>

<script>
import SettingLayout from './Layout.vue';

export default {
    components: {
        SettingLayout,
    },
    props: {
        data: {},
    },
    data: function () {
        return {
            inner: {...this.data},
        };
    },
    methods: {
        submit() {
            this.$inertia.post('/setting/bill', this.inner, {
                onSuccess: () => this.$success('Einstellungen gespeichert.'),
            });
        },
    },
};
</script>
