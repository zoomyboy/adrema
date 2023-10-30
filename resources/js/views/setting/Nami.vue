<template>
    <page-layout>
        <template #right>
            <f-save-button form="namisettingform"></f-save-button>
        </template>
        <setting-layout>
            <form id="namisettingform" class="grow p-6 grid grid-cols-2 gap-3 items-start content-start"
                @submit.prevent="submit">
                <div class="col-span-full text-gray-100 mb-3">
                    <p class="text-sm">Hier kannst du deine Zugangsdaten zu NaMi anpassen, falls sich z.B. dein Passwort
                        geändert hat.</p>
                </div>
                <f-text id="mglnr" v-model="inner.mglnr" label="Mitgliedsnummer" name="mglnr"></f-text>
                <f-text id="default_group_id" v-model="inner.default_group_id" label="Standard-Gruppierung"
                    name="default_group_id"></f-text>
                <f-text id="password" v-model="inner.password" label="Passwort" name="password" type="password"></f-text>
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
            inner: { ...this.data },
        };
    },
    methods: {
        submit() {
            this.$inertia.post('/setting/nami', this.inner, {
                onSuccess: () => this.$success('Einstellungen gespeichert.'),
            });
        },
    },
};
</script>
