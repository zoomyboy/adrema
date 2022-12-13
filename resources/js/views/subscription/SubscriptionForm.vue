<template>
    <form id="subedit" class="p-3 grid gap-3" @submit.prevent="submit">
        <save-button form="subedit"></save-button>
        <box heading="Beitrag">
            <div class="grid gap-4 sm:grid-cols-2">
                <f-text id="name" v-model="inner.name" label="Name" size="sm" required></f-text>
                <f-select
                    id="fee_id"
                    name="fee_id"
                    :options="fees"
                    v-model="inner.fee_id"
                    label="Nami-Beitrag"
                    size="sm"
                    required
                ></f-select>
            </div>
        </box>
        <box heading="Positionen">
            <div class="flex flex-col space-y-4">
                <div v-for="(pos, index) in inner.children" :key="index" class="flex space-x-2 items-end">
                    <f-text :id="`name-${index}`" v-model="pos.name" label="Name" size="sm" required></f-text>
                    <f-text
                        :id="`amount-${index}`"
                        v-model="pos.amount"
                        label="Beitrag"
                        size="sm"
                        mode="area"
                        required
                    ></f-text>
                    <a
                        href="#"
                        @click.prevent="inner.children.splice(index, 1)"
                        class="btn btn-sm btn-danger icon flex-none"
                    >
                        <svg-sprite src="trash" class="w-5 h-5"></svg-sprite>
                    </a>
                </div>
                <a
                    href="#"
                    @click.prevent="inner.children.push({name: '', amount: 0})"
                    class="btn btn-sm flex btn-primary flex self-start mt-4"
                >
                    <svg-sprite src="plus" class="w-5 h-5"></svg-sprite>
                    Position hinzufügen
                </a>
            </div>
        </box>
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
