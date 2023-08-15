<template>
    <page-layout>
        <template #toolbar>
            <page-toolbar-button color="primary" icon="plus" @click.prevent="model = { ...meta.default }">Neue
                Verbindung</page-toolbar-button>
        </template>
        <ui-popup v-if="model !== null" :heading="model.id ? 'Verbindung bearbeiten' : 'Neue Verbindung'"
            @close="model = null">
            <form @submit.prevent="submit">
                <section class="grid grid-cols-2 gap-3 mt-6">
                    <f-text id="name" v-model="model.name" name="name" label="Bezeichnung" required></f-text>
                    <f-text id="domain" v-model="model.domain" name="domain" label="Domain" required></f-text>
                    <f-select id="type" :model-value="model.type.cls" label="Typ" name="type" :options="meta.types"
                        :placeholder="''" required @update:model-value="
                            model.type = {
                                cls: $event,
                                params: { ...getType($event).defaults },
                            }
                        "></f-select>
                    <template v-for="(field, index) in getType(model.type.cls).fields">
                        <f-text v-if="field.type === 'text' || field.type === 'password' || field.type === 'email'"
                            :id="field.name" :key="index" v-model="model.type.params[field.name]" :label="field.label"
                            :type="field.type" :name="field.name" :required="field.is_required"></f-text>
                    </template>
                </section>
                <section class="flex mt-4 space-x-2">
                    <ui-button type="submit" class="btn-danger">Speichern</ui-button>
                    <ui-button class="btn-primary" @click.prevent="model = null">Abbrechen</ui-button>
                </section>
            </form>
        </ui-popup>
        <setting-layout>
            <div class="w-full h-full pb-6">
                <table cellspacing="0" cellpadding="0" border="0" class="custom-table custom-table-sm hidden md:table">
                    <thead>
                        <th>Bezeichnung</th>
                        <th>Domain</th>
                        <th>Typ</th>
                        <th>Prüfung</th>
                        <th>Aktion</th>
                    </thead>

                    <tr v-for="(gateway, index) in data" :key="index">
                        <td v-text="gateway.name"></td>
                        <td v-text="gateway.domain"></td>
                        <td v-text="gateway.type_human"></td>
                        <td>
                            <ui-boolean-display :value="gateway.works" long-label="Verbindungsstatus"
                                :label="gateway.works ? 'Verbindung erfolgreich' : 'Verbindung fehlgeschlagen'"></ui-boolean-display>
                        </td>
                        <td>
                            <a v-tooltip="`Bearbeiten`" href="#" class="inline-flex btn btn-warning btn-sm"
                                @click.prevent="model = { ...gateway }"><ui-sprite src="pencil"></ui-sprite></a>
                        </td>
                    </tr>
                </table>

                <div class="px-6">
                    <ui-pagination class="mt-4" :value="meta" :only="['data']"></ui-pagination>
                </div>
            </div>
        </setting-layout>
    </page-layout>
</template>

<script setup>
import { ref, inject } from 'vue';
import { indexProps, useIndex } from '../../composables/useIndex.js';
import SettingLayout from '../setting/Layout.vue';

const props = defineProps(indexProps);
const { meta, data, reload } = useIndex(props.data, 'mailgateway');
const model = ref(null);
const axios = inject('axios');

function getType(type) {
    return meta.value.types.find((t) => t.id === type);
}
async function submit() {
    await axios[model.value.id ? 'patch' : 'post'](model.value.id ? model.value.links.update : meta.value.links.store, model.value);

    reload();
    model.value = null;
}
</script>
