<template>
    <page-layout>
        <template #right>
            <f-save-button form="modulesettingform"></f-save-button>
        </template>
        <setting-layout>
            <form id="modulesettingform" class="grow p-6 grid grid-cols-2 gap-3 items-start content-start"
                @submit.prevent="submit">
                <div class="col-span-full text-gray-100 mb-3">
                    <p class="text-sm">Hier kannst du Funktionen innerhalb von Adrema (Module) aktivieren oder deaktivieren
                        und so den Funktionsumfang auf deine Bedürfnisse anpassen.</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <f-switch v-for="module in meta.modules" :id="module.id" v-model="inner.modules" :value="module.id"
                        size="sm" name="modules" :label="module.name"></f-switch>
                </div>
            </form>
        </setting-layout>
    </page-layout>
</template>

<script>
import SettingLayout from './Layout.vue';

export default {
    components: {
        SettingLayout,
    },
    props: {
        data: {
            type: Object,
            default: () => {
                return {};
            },
        },
    },
    data: function () {
        return {
            inner: { ...this.data.data },
            meta: { ...this.data.meta },
        };
    },
    methods: {
        submit() {
            this.$inertia.post('/setting/module', this.inner, {
                onSuccess: () => this.$success('Einstellungen gespeichert.'),
            });
        },
    },
};
</script>
