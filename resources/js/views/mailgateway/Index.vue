<template>
    <page-layout>
        <template #toolbar>
            <page-toolbar-button color="primary" icon="plus" @click.prevent="create">Neue Verbindung</page-toolbar-button>
        </template>
        <ui-popup v-if="single !== null" :heading="single.id ? 'Verbindung bearbeiten' : 'Neue Verbindung'" @close="cancel">
            <form @submit.prevent="submit">
                <section class="grid grid-cols-2 gap-3 mt-6">
                    <f-text id="name" v-model="single.name" name="name" label="Bezeichnung" required></f-text>
                    <f-text id="domain" v-model="single.domain" name="domain" label="Domain" required></f-text>
                    <f-select id="type" :model-value="single.type.cls" label="Typ" name="type" :options="meta.types"
                        :placeholder="''" required @update:model-value="
                            single.type = {
                                cls: $event,
                                params: { ...getType($event).defaults },
                            }
                        "></f-select>
                    <template v-for="(field, index) in getType(single.type.cls).fields">
                        <f-text v-if="field.type === 'text' || field.type === 'password' || field.type === 'email'"
                            :id="field.name" :key="index" v-model="single.type.params[field.name]" :label="field.label"
                            :type="field.type" :name="field.name" :required="field.is_required"></f-text>
                    </template>
                </section>
                <section class="flex mt-4 space-x-2">
                    <ui-button type="submit" class="btn-danger">Speichern</ui-button>
                    <ui-button class="btn-primary" @click.prevent="single = null">Abbrechen</ui-button>
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
                                @click.prevent="edit(gateway)"><ui-sprite src="pencil"></ui-sprite></a>
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
import { indexProps, useIndex } from '../../composables/useInertiaApiIndex.js';
import SettingLayout from '../setting/Layout.vue';

const props = defineProps(indexProps);
const { meta, data, create, edit, cancel, single, submit } = useIndex(props.data, 'mailgateway');

function getType(type) {
    return meta.value.types.find((t) => t.id === type);
}
</script>
