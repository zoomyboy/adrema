<template>
    <form class="flex grow relative" @submit.prevent="submit">
        <!-- ********************************* Tabs ********************************** -->
        <v-tabs v-model="active" :entries="menu">
            <div slot="bottom">
                <button type="submit" class="mt-3 btn block w-full btn-primary">Speichern</button>
            </div>
        </v-tabs>
        <div class="grow p-6 grid grid-cols-2 gap-3 items-start content-start" v-show="menuTitle === 'Rechnung'">
            <f-text
                label="Absender"
                hint="Absender-Name, i.d.R. der kurze Stammesname"
                name="bill_from"
                id="bill_from"
                v-model="inner.bill_from"
            ></f-text>
            <f-text
                label="Absender (lang)"
                v-model="inner.bill_from_long"
                name="bill_from_long"
                id="bill_from_long"
                hint="Absender-Name in Kurzform, i.d.R. der Stammesname"
            ></f-text>
            <h2 class="text-lg font-semibold text-gray-300 col-span-2 mt-5">Kontaktdaten</h2>
            <div class="col-span-2 text-gray-300 text-sm">
                Diese Kontaktdaten stehen im Absender-Bereich auf der Rechnung.
            </div>
            <f-text label="Straße" v-model="inner.bill_address" name="bill_address" id="bill_address"></f-text>
            <f-text label="PLZ" v-model="inner.bill_zip" name="bill_zip" id="bill_zip"></f-text>
            <f-text label="Ort" v-model="inner.bill_place" name="bill_place" id="bill_place"></f-text>
            <f-text label="E-Mail-Adresse" v-model="inner.bill_email" name="bill_email" id="bill_email"></f-text>
            <f-text label="Telefonnummer" v-model="inner.bill_mobile" name="bill_mobile" id="bill_mobile"></f-text>
            <f-text label="Webseite" v-model="inner.bill_website" name="bill_website" id="bill_website"></f-text>
        </div>
    </form>
</template>

<script>
export default {
    data: function () {
        return {
            inner: {},
            active: 0,
            menu: [{id: 'bill', title: 'Rechnung'}],
        };
    },
    props: {
        data: {},
    },
    methods: {
        submit() {
            this.$inertia.post('/setting', this.inner);
        },
    },
    components: {
        'v-tabs': () => import('../../components/VTabs.vue'),
    },
    computed: {
        menuTitle() {
            return this.menu[this.active].title;
        },
    },
    created() {
        this.inner = this.data;
    },
};
</script>
