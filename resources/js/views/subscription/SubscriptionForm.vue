<template>
    <page-layout>
        <template #toolbar>
            <page-toolbar-button :href="meta.links.index" color="primary" icon="undo">zurück</page-toolbar-button>
        </template>
        <form id="subedit" class="p-3 grid gap-3" @submit.prevent="submit">
            <f-save-button form="subedit"></f-save-button>
            <ui-box heading="Beitrag">
                <div class="grid gap-4 sm:grid-cols-2">
                    <f-text id="name" v-model="inner.name" label="Name" size="sm" required></f-text>
                    <f-select id="fee_id" name="fee_id" :options="fees" v-model="inner.fee_id" label="Nami-Beitrag" size="sm" required></f-select>
                    <f-switch id="split" label="Rechnung aufsplitten" v-model="inner.split" size="sm"></f-switch>
                    <f-switch id="for_promise" label="Für Versprechen benutzen" v-model="inner.for_promise" size="sm"></f-switch>
                </div>
            </ui-box>
            <ui-box heading="Positionen">
                <div class="flex flex-col space-y-4">
                    <div v-for="(pos, index) in inner.children" :key="index" class="flex space-x-2 items-end">
                        <f-text :id="`name-${index}`" v-model="pos.name" label="Name" size="sm" required></f-text>
                        <f-text :id="`amount-${index}`" v-model="pos.amount" label="Beitrag" size="sm" mode="area" required></f-text>
                        <a href="#" @click.prevent="inner.children.splice(index, 1)" class="btn btn-sm btn-danger icon flex-none">
                            <svg-sprite src="trash" class="w-5 h-5"></svg-sprite>
                        </a>
                    </div>
                    <a href="#" @click.prevent="inner.children.push({name: '', amount: 0})" class="btn btn-sm flex btn-primary flex self-start mt-4">
                        <svg-sprite src="plus" class="w-5 h-5"></svg-sprite>
                        Position hinzufügen
                    </a>
                </div>
            </ui-box>
        </form>
    </page-layout>
</template>

<script>
export default {
    data: function () {
        return {
            inner: {...this.data},
        };
    },

    props: {
        data: {},
        fees: {},
        mode: {},
        meta: {},
    },

    methods: {
        submit() {
            this.mode === 'create' ? this.$inertia.post(`/subscription`, this.inner) : this.$inertia.patch(`/subscription/${this.inner.id}`, this.inner);
        },
    },
};
</script>
