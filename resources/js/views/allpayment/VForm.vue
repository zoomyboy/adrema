<template>
    <page-layout>
        <form class="p-6 grid gap-4 justify-start" @submit.prevent="submit">
            <f-text id="year" v-model="inner.year" label="Jahr" required></f-text>

            <f-switch id="for_promise" label="Versprechen einbeziehen" v-model="inner.for_promise" size="sm"></f-switch>

            <button type="submit" class="btn btn-primary">Absenden</button>
        </form>
    </page-layout>
</template>

<script>
export default {
    data: function () {
        return {
            inner: {
                for_promise: false,
            },
        };
    },

    props: {},

    methods: {
        submit() {
            this.$inertia.post(`/allpayment`, this.inner);
        },
    },
};
</script>
