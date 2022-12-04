<template>
    <form
        id="billsettingform"
        class="grow p-6 grid grid-cols-2 gap-3 items-start content-start"
        @submit.prevent="submit"
    >
        <save-button form="billsettingform"></save-button>
        <f-text
            label="Absender"
            hint="Absender-Name in Kurzform, i.d.R. der kurze Stammesname"
            name="from"
            id="from"
            v-model="inner.from"
        ></f-text>
        <f-text
            label="Absender (lang)"
            v-model="inner.from_long"
            name="from_long"
            id="from_long"
            hint="Absender-Name in Langform, i.d.R. der Stammesname"
        ></f-text>
        <h2 class="text-lg font-semibold text-gray-300 col-span-2 mt-5">Kontaktdaten</h2>
        <div class="col-span-2 text-gray-300 text-sm">
            Diese Kontaktdaten stehen im Absender-Bereich auf der Rechnung.
        </div>
        <f-text label="Straße" v-model="inner.address" name="address" id="address"></f-text>
        <f-text label="PLZ" v-model="inner.zip" name="zip" id="zip"></f-text>
        <f-text label="Ort" v-model="inner.place" name="place" id="place"></f-text>
        <f-text label="E-Mail-Adresse" v-model="inner.email" name="email" id="email"></f-text>
        <f-text label="Telefonnummer" v-model="inner.mobile" name="mobile" id="mobile"></f-text>
        <f-text label="Webseite" v-model="inner.website" name="website" id="website"></f-text>
        <f-text label="IBAN" v-model="inner.iban" name="iban" id="iban"></f-text>
        <f-text label="BIC" v-model="inner.bic" name="bic" id="bic"></f-text>
    </form>
</template>

<script>
import AppLayout from '../../layouts/AppLayout.vue';
import SettingLayout from './Layout.vue';

export default {
    layout: [AppLayout, SettingLayout],

    data: function () {
        return {
            inner: {},
        };
    },
    props: {
        data: {},
    },
    methods: {
        submit() {
            this.$inertia.post('/setting/bill', this.inner);
        },
    },
    created() {
        this.inner = this.data;
    },
};
</script>
