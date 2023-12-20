<template>
    <page-layout>
        <template #toolbar>
            <page-toolbar-button :href="meta.links.index" color="primary" icon="undo">zurück</page-toolbar-button>
        </template>
        <template #right>
            <f-save-button form="subedit"></f-save-button>
        </template>
        <form id="subedit" class="p-3 grid gap-3" @submit.prevent="submit">
            <ui-box heading="Beitrag">
                <div class="grid gap-4 sm:grid-cols-2">
                    <f-text id="name" v-model="inner.name" label="Name" size="sm" required></f-text>
                    <f-select id="fee_id" v-model="inner.fee_id" name="fee_id" :options="fees" label="Nami-Beitrag"
                        size="sm" required></f-select>
                </div>
            </ui-box>
            <ui-box heading="Positionen">
                <div class="flex flex-col space-y-4">
                    <div v-for="(pos, index) in inner.children" :key="index" class="flex space-x-2 items-end">
                        <f-text :id="`name-${index}`" v-model="pos.name" label="Name" size="sm" required></f-text>
                        <f-text :id="`amount-${index}`" v-model="pos.amount" label="Beitrag" size="sm" mode="area"
                            required></f-text>
                        <a href="#" class="btn btn-sm btn-danger icon flex-none"
                            @click.prevent="inner.children.splice(index, 1)">
                            <ui-sprite src="trash" class="w-5 h-5"></ui-sprite>
                        </a>
                    </div>
                    <a href="#" class="btn btn-sm flex btn-primary flex self-start mt-4"
                        @click.prevent="inner.children.push({ name: '', amount: 0 })">
                        <ui-sprite src="plus" class="w-5 h-5"></ui-sprite>
                        Position hinzufügen
                    </a>
                </div>
            </ui-box>
        </form>
    </page-layout>
</template>

<script>
export default {

    props: {
        data: {},
        fees: {},
        mode: {},
        meta: {},
    },
    data: function () {
        return {
            inner: { ...this.data },
        };
    },

    methods: {
        submit() {
            this.mode === 'create' ? this.$inertia.post(`/subscription`, this.inner) : this.$inertia.patch(`/subscription/${this.inner.id}`, this.inner);
        },
    },
};
</script>
