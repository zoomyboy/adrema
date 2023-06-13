<template>
    <page-layout>
        <template #toolbar>
            <page-toolbar-button :href="meta.links.index" color="primary" icon="undo">Zurück</page-toolbar-button>
        </template>
        <form id="form" class="p-3 grid gap-3" @submit.prevent="submit">
            <f-save-button form="form"></f-save-button>
            <ui-box heading="Filterregeln">
                <div class="grid gap-4 sm:grid-cols-2">
                    <f-select id="activity_id" name="activity_id" :options="meta.activities" v-model="model.activity_id" @input="reload" label="Tätigkeit" size="sm" required></f-select>
                    <f-select id="subactivity_id" name="subactivity_id" :options="meta.activities" v-model="model.subactivity_id" @input="reload" label="Unterttätigkeit" size="sm" required></f-select>
                </div>
            </ui-box>
            <ui-box heading="Mitglieder">
                <div class="flex flex-col space-y-4">
                    <div v-for="(pos, index) in model.children" :key="index" class="flex space-x-2 items-end">
                        <f-text :id="`name-${index}`" v-model="pos.name" label="Name" size="sm" required></f-text>
                        <f-text :id="`amount-${index}`" v-model="pos.amount" label="Beitrag" size="sm" mode="area" required></f-text>
                        <a href="#" @click.prevent="model.children.splice(index, 1)" class="btn btn-sm btn-danger icon flex-none">
                            <svg-sprite src="trash" class="w-5 h-5"></svg-sprite>
                        </a>
                    </div>
                </div>
            </ui-box>
        </form>
    </page-layout>
</template>

<script>
export default {
    data: function () {
        return {
            model: this.mode === 'create' ? {...this.meta.default_model} : {...this.data},
            members: [],
        };
    },

    props: {
        data: {},
        mode: {},
        meta: {},
    },

    methods: {
        async reload() {
            this.members = (
                await this.axios.post('/api/member', {
                    filter: this.model,
                })
            ).data;
        },
    },

    async created() {},
};
</script>
