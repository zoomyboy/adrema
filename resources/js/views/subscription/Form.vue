<template>
    <form class="p-6 grid gap-4 justify-start" @submit.prevent="submit">

        <f-text id="name" v-model="inner.name" label="Name" required></f-text>
        <f-select id="fee_id" :options="fees" v-model="inner.fee_id" label="Nami-Beitrag" required></f-select>
        <f-text id="amount" v-model="inner.amount" label="Interner Beitrag" required></f-text>

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
        data: {},
        fees: {},
        mode: {},
    },

    methods: {
        submit() {
            this.mode === 'create'
                ? this.$inertia.post(`/subscription`, this.inner)
                : this.$inertia.patch(`/subscription/${this.inner.id}`, this.inner);
        }
    },

    created() {
        this.inner = this.data;
    }
};
</script>
