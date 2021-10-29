<template>
    <div class="p-6">
        <p class="text-white">Diese Funktion erstellt ein PDF mit allen noch nicht versendenden Rechnungen bei den Mitgliedern die Post als Versandweg haben.</p>
        <p class="text-white">Die Rechnungen werden automatisch auf "Rechnung gestellt" aktualisiert.</p>
        <a :href="link.href" target="_BLANK" v-for="link in links" class="btn btn-primary mt-3 inline-block" v-text="link.label"></a>
    </div>
</template>

<script>
export default {
    data: function() {
        return {
            inner: {},
        };
    },

    props: {
        links: {}
    },

    methods: {
        submit() {
            this.$inertia.post(`/allpayment`, this.inner)
        }
    }

};
</script>
