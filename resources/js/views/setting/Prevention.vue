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
                <div class="grid gap-4 mt-2">
                    <f-editor id="frommail" v-model="data.formmail" label="E-Mail für Veranstaltungs-TN"></f-editor>
                </div>
            </form>
        </setting-layout>
    </page-layout>
</template>

<script lang="js" setup>
import { ref } from 'vue';
import { useApiIndex } from '../../composables/useApiIndex.js';
import SettingLayout from '../setting/Layout.vue';

const { axios, data, reload } = useApiIndex('/api/prevention', 'prevention');
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
