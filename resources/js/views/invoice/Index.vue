<template>
    <page-layout>
        <template #toolbar>
            <page-toolbar-button color="primary" icon="plus" @click="model = { ...props.meta.default }">Rechnung
                anlegen</page-toolbar-button>
            <page-toolbar-button color="primary" icon="plus" @click="massstore = { year: '' }">Massenrechnung
                anlegen</page-toolbar-button>
        </template>
        <ui-popup v-if="massstore !== null" heading="Massenrechnung anlegen" @close="massstore = null">
            <form @submit.prevent="sendMassstore">
                <section class="grid grid-cols-2 gap-3 mt-6">
                    <f-text id="year" v-model="massstore.year" name="year" label="Jahr" required></f-text>
                </section>
                <section class="flex mt-4 space-x-2">
                    <ui-button type="submit" class="btn-danger">Speichern</ui-button>
                    <ui-button class="btn-primary" @click.prevent="massstore = null">Abbrechen</ui-button>
                </section>
            </form>
        </ui-popup>
        <table cellspacing="0" cellpadding="0" border="0" class="custom-table custom-table-sm">
            <thead>
                <th>Empfänger</th>
                <th>Gesamtbetrag</th>
                <th>Status</th>
                <th>Gesendet am</th>
                <th>Rechnungsweg</th>
                <th></th>
            </thead>

            <tr v-for="(invoice, index) in data" :key="index">
                <td>
                    <div v-text="invoice.to_name"></div>
                </td>
                <td>
                    <div v-text="invoice.sum_human"></div>
                </td>
                <td>
                    <div v-text="invoice.status"></div>
                </td>
                <td>
                    <div v-text="invoice.sent_at_human"></div>
                </td>
                <td>
                    <div v-text="invoice.via"></div>
                </td>
                <td></td>
            </tr>
        </table>
        <div class="px-6">
            <ui-pagination class="mt-4" :value="meta" @reload="reloadPage"></ui-pagination>
        </div>
    </page-layout>
</template>

<script setup>
import { ref } from 'vue';
import { indexProps, useIndex } from '../../composables/useIndex.js';
const props = defineProps(indexProps);
var { axios, meta, data, reloadPage } = useIndex(props.data, 'invoice');
const massstore = ref(null);

async function sendMassstore() {
    await axios.post(meta.value.links['mass-store'], massstore.value);
    massstore.value = null;
}
</script>
