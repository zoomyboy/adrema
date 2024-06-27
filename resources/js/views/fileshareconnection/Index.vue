<template>
    <page-layout>
        <template #toolbar>
            <page-toolbar-button color="primary" icon="plus" @click.prevent="create">Neue Verbindung</page-toolbar-button>
        </template>
        <ui-popup v-if="single !== null" :heading="single.id ? 'Verbindung bearbeiten' : 'Neue Verbindung'" @close="cancel">
            <form @submit.prevent="submit">
                <section class="grid grid-cols-2 gap-3 mt-6">
                    <f-text id="name" v-model="single.name" name="name" label="Bezeichnung" required></f-text>
                    <f-select id="type" :model-value="single.type" label="Typ" name="type" :options="meta.types" required
                        @update:model-value="
                            single = {
                                ...single,
                                type: $event,
                                config: { ...getType($event).defaults },
                            }
                        "></f-select>
                    <template v-for="(field, index) in getType(single.type).fields">
                        <f-text v-if="field.type === 'text' || field.type === 'password' || field.type === 'email'"
                            :id="field.key" :key="index" v-model="single.config[field.key]" :label="field.label"
                            :type="field.type" :name="field.key" required></f-text>
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
                        <th>Typ</th>
                        <th>Prüfung</th>
                        <th>Aktion</th>
                    </thead>

                    <tr v-for="(connection, index) in data" :key="index">
                        <td v-text="connection.name"></td>
                        <td v-text="connection.type_human"></td>
                        <td>
                            <ui-boolean-display :value="connection.is_active" long-label="Verbindungsstatus"
                                :label="connection.is_active ? 'Verbindung erfolgreich' : 'Verbindung fehlgeschlagen'"></ui-boolean-display>
                        </td>
                        <td>
                            <a v-tooltip="`Bearbeiten`" href="#" class="inline-flex btn btn-warning btn-sm"
                                @click.prevent="edit(connection)"><ui-sprite src="pencil"></ui-sprite></a>
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
    if (!type) {
        return {
            fields: [],
        };
    }
    return meta.value.types.find((t) => t.id === type);
}
</script>
