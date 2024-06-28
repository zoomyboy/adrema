<template>
    <page-layout>
        <template #right>
            <f-save-button form="formsettingsform"></f-save-button>
        </template>
        <setting-layout>
            <form id="formsettingsform" class="grow p-6 grid grid-cols-2 gap-3 items-start content-start" @submit.prevent="submit">
                <div class="col-span-full text-gray-100 mb-3">
                    <p class="text-sm">Hier kannst du Einstellungen für Anmeldeformulare setzen.</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <f-text id="register_url" v-model="inner.register_url" label="Formular-Link"></f-text>
                    <f-text id="clear_cache_url" v-model="inner.clear_cache_url" label="Frontend-Cache-Url"></f-text>
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
            inner: {...this.data.data},
        };
    },
    methods: {
        submit() {
            this.$inertia.post('/setting/form', this.inner, {
                onSuccess: () => this.$success('Einstellungen gespeichert.'),
            });
        },
    },
};
</script>
