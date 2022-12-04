<template>
    <form id="subedit" class="p-6 grid gap-4 justify-start" @submit.prevent="submit">
        <save-button form="subedit"></save-button>
        <f-text id="name" v-model="inner.name" label="Name" required></f-text>
        <f-select
            id="fee_id"
            name="fee_id"
            :options="fees"
            v-model="inner.fee_id"
            label="Nami-Beitrag"
            required
        ></f-select>
        <f-text id="amount" v-model="inner.amount" label="Interner Beitrag" mode="area" required></f-text>
    </form>
</template>

<script>
export default {
    data: function () {
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
        },
    },

    created() {
        this.inner = this.data;
    },
};
</script>
