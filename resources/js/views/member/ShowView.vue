<template>
    <div class="p-6 grid gap-6 this-grid">
        <!-- ****************************** Stammdaten ******************************* -->
        <div class="bg-gray-800 p-3 grid grid-cols-2 justify-start gap-3 rounded-lg">
            <heading class="col-span-full">Stammdaten</heading>
            <key-value class="col-span-2" label="Name" :value="inner.fullname"></key-value>
            <key-value class="col-span-2" label="Adresse" :value="inner.full_address"></key-value>
            <key-value label="Geburtsdatum" :value="inner.birthday_human"></key-value>
            <key-value label="Alter" :value="inner.age"></key-value>
            <key-value label="Bundesland" :value="inner.region.name"></key-value>
            <key-value label="Nationalität" :value="inner.nationality.name"></key-value>
            <key-value
                v-show="inner.other_country"
                label="Andere Staatsangehörigkeit"
                :value="inner.other_country"
            ></key-value>
        </div>

        <!-- ******************************** Kontakt ******************************** -->
        <div class="bg-gray-800 p-3 grid justify-start gap-3 rounded-lg">
            <heading class="col-span-full">Kontakt</heading>
            <key-value
                v-show="inner.main_phone"
                label="Telefon Eltern"
                :value="inner.main_phone"
                type="tel"
            ></key-value>
            <key-value
                v-show="inner.mobile_phone"
                label="Handy Eltern"
                :value="inner.mobile_phone"
                type="tel"
            ></key-value>
            <key-value
                v-show="inner.work_phone"
                label="Telefon Eltern geschäftlich"
                :value="inner.work_phone"
                type="tel"
            ></key-value>
            <key-value
                v-show="inner.children_phone"
                label="Telefon Kind"
                :value="inner.children_phone"
                type="tel"
            ></key-value>
            <key-value v-show="inner.email" label="E-Mail-Adresse Kind" :value="inner.email" type="email"></key-value>
            <key-value
                v-show="inner.email_parents"
                label="E-Mail-Adresse Eltern"
                :value="inner.email_parents"
                type="email"
            ></key-value>
            <key-value v-show="inner.fax" label="Fax" :value="inner.fax" type="tel"></key-value>
        </div>
    </div>
</template>

<script>
export default {
    data: function () {
        return {
            inner: {},
        };
    },

    methods: {},

    props: {
        data: {},
    },

    components: {
        'key-value': () => import('./KeyValue'),
        'heading': () => import('./Heading'),
    },

    created() {
        this.inner = this.data;
    },
};
</script>

<style scoped>
.this-grid {
    grid-template-columns: max-content max-content 1fr;
}
</style>
