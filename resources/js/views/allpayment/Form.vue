<template>
    <form class="p-6 grid gap-4 justify-start" @submit.prevent="submit">

        <f-text id="year" v-model="inner.year" label="Jahr" required></f-text>

        <button type="submit" class="btn btn-primary">Absenden</button>
    </form>
</template>

<script>
export default {
    data: function() {
        return {
            inner: {},
        };
    },

    props: {
    },

    methods: {
        submit() {
            this.$inertia.post(`/allpayment`, this.inner)
        }
    }

};
</script>
