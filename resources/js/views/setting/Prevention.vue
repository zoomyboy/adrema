<template>
    <page-layout>
        <template #right>
            <f-save-button form="preventionform"></f-save-button>
        </template>

        <setting-layout v-if="loaded">
            <form id="preventionform" class="grow p-6" @submit.prevent="submit">
                <div class="col-span-full text-gray-100 mb-3">
                    <p class="text-sm">Hier kannst du Einstellungen zu Prävention setzen.</p>
                </div>
                <ui-tabs v-model="active" class="mt-2" :entries="tabs"></ui-tabs>
                <div v-if="active === 0">
                    <f-editor v-if="active === 0" id="formmail" v-model="data.formmail" label="E-Mail für Veranstaltungs-TN"></f-editor>
                </div>
                <div v-if="active === 1" class="grid gap-6">
                    <f-switch id="active" v-model="data.active" name="active" label="Regelmäßig an Präventionsunterlagen erinnern"></f-switch>
                    <div class="flex gap-6">
                        <f-text id="weeks" v-model="data.weeks" label="Vor Ablauf X Wochen vorher erinnern" type="number" />
                        <f-text id="fresh_remember_interval" v-model="data.freshRememberInterval" label="Bei Ablauf alle X Wochen erinnern" type="number" />
                    </div>
                    <f-editor v-if="active === 1" id="yearlymail" v-model="data.yearlymail" label="Jährliche Präventions-Erinnerung"></f-editor>
                    <f-member-filter id="yearly_member_filter" v-model="data.yearlyMemberFilter" label="nur für folgende Mitglieder erlauben" />
                    <f-multipleselect id="prevent_against" v-model="data.preventAgainst" :options="meta.preventAgainsts" label="An diese Dokumente erinnern" size="sm"></f-multipleselect>
                </div>
            </form>
        </setting-layout>
    </page-layout>
</template>

<script lang="js" setup>
import { ref } from 'vue';
import { useApiIndex } from '../../composables/useApiIndex.js';
import SettingLayout from '../setting/Layout.vue';

const tabs = [
    { title: 'für Veranstaltungen' },
    { title: 'Jährlich' },
];
const active = ref(0);

const { axios, data, meta, reload } = useApiIndex('/api/prevention', 'prevention');
const loaded = ref(false);

async function load() {
    await reload();
    loaded.value = true;
}

async function submit() {
    await axios.post('/api/prevention', { ...data.value });
}

load();
</script>
