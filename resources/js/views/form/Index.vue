<template>
    <page-layout>
        <template #toolbar>
            <page-toolbar-button :href="meta.links.formtemplate_index" color="primary" icon="event">Vorlagen</page-toolbar-button>
            <page-toolbar-button color="primary" icon="plus" @click.prevent="create">Veranstaltung erstellen</page-toolbar-button>
        </template>

        <ui-popup v-if="deleting !== null" :heading="`Veranstaltung ${deleting.name} löschen?`" @close="deleting = null">
            <div>
                <p class="mt-4">Diese Veranstaltung löschen?</p>
                <div class="grid grid-cols-2 gap-3 mt-6">
                    <a
                        href="#"
                        class="text-center btn btn-danger"
                        @click.prevent="
                            remove(deleting);
                            deleting = null;
                        "
                        >Veranstaltung löschen</a
                    >
                    <a href="#" class="text-center btn btn-primary" @click.prevent="deleting = null">Abbrechen</a>
                </div>
            </div>
        </ui-popup>

        <ui-popup v-if="single !== null && single.config === null" heading="Vorlage auswählen" @close="cancel">
            <div class="mt-3 grid gap-3 grid-cols-2">
                <a v-for="(template, index) in meta.templates" :key="index" class="py-2 px-3 border rounded bg-zinc-800 hover:bg-zinc-700 transition" href="#" @click.prevent="setTemplate(template)">
                    <span v-text="template.name"></span>
                </a>
            </div>
        </ui-popup>

        <ui-popup v-if="showing !== null" :heading="`Teilnehmende für ${showing.name}`" full @close="showing = null">
            <participants :has-nami-field="showing.has_nami_field" :root-url="showing.links.participant_root_index" :url="showing.links.participant_index"> </participants>
        </ui-popup>

        <ui-popup v-if="single !== null && single.config !== null" :heading="`Veranstaltung ${single.id ? 'bearbeiten' : 'erstellen'}`" full @close="cancel">
            <div class="flex flex-col mt-3">
                <ui-tabs v-model="active" :entries="tabs"></ui-tabs>
                <div v-show="active === 0" class="grid grid-cols-2 gap-3">
                    <div class="flex space-x-3">
                        <f-text id="name" v-model="single.name" class="grow" label="Name" required></f-text>
                        <f-switch id="is_active" v-model="single.is_active" name="is_active" label="Aktiv"></f-switch>
                        <f-switch id="is_private" v-model="single.is_private" name="is_private" label="Privat"></f-switch>
                    </div>
                    <f-singlefile
                        id="header_image"
                        v-model="single.header_image"
                        label="Bild"
                        name="header_image"
                        parent-name="form"
                        :parent-id="single.id"
                        collection="headerImage"
                        required
                    ></f-singlefile>
                    <f-text id="from" v-model="single.from" type="date" label="Von" required></f-text>
                    <f-text id="to" v-model="single.to" type="date" label="Bis" required></f-text>
                    <f-textarea
                        id="excerpt"
                        v-model="single.excerpt"
                        hint="Gebe hier eine kurze Beschreibung für die Veranstaltungs-Übersicht ein (Maximal 130 Zeichen)."
                        label="Auszug"
                        rows="5"
                        required
                    ></f-textarea>
                    <f-editor id="description" v-model="single.description" name="description" label="Beschreibung" rows="10" required></f-editor>
                </div>
                <div v-if="active === 1">
                    <ui-note class="mt-2"> Sobald sich der erste Teilnehmer für die Veranstaltung angemeldet hat, kann dieses Formular nicht mehr geändert werden. </ui-note>
                    <form-builder v-model="single.config" :meta="meta"></form-builder>
                </div>
                <div v-show="active === 2" class="grid grid-cols-[1fr_300px] gap-3">
                    <ui-note class="mt-2 col-span-full">
                        Hier kannst du die E-Mail anpassen, die nach der Anmeldung an den Teilnehmer verschickt wird.<br />
                        Es gibt dafür einen ersten E-Mail-Teil und einen zweiten E-Mail-Teil. Dazwischen werden die Daten des Teilnehmers aufgelistet.<br />
                        Die Anrede ("Hallo Max Mustermann") wird automatisch an den Anfang gesetzt.<br />
                        Außerdem kannst du Dateien hochladen, die automatisch mit angehangen werden.
                    </ui-note>
                    <div>
                        <ui-tabs v-model="activeMailTab" :entries="mailTabs"></ui-tabs>
                        <f-editor v-if="activeMailTab === 0" id="mail_top" v-model="single.mail_top" name="mail_top" label="E-Mail-Teil 1" rows="8" conditions required>
                            <template #conditions="{data, resolve}">
                                <conditions :single="single" :value="data" @save="resolve"> </conditions>
                            </template>
                        </f-editor>
                        <f-editor v-if="activeMailTab === 1" id="mail_bottom" v-model="single.mail_bottom" name="mail_bottom" label="E-Mail-Teil 2" rows="8" conditions required>
                            <template #conditions="{data, resolve}">
                                <conditions :single="single" :value="data" @save="resolve"> </conditions>
                            </template>
                        </f-editor>
                    </div>
                    <f-multiplefiles
                        id="mailattachments"
                        v-model="single.mailattachments"
                        label="Anhänge"
                        name="mailattachments"
                        parent-name="form"
                        :parent-id="single.id"
                        collection="mailattachments"
                        class="row-span-2"
                    >
                        <template #buttons="{file, buttonClass, iconClass}">
                            <a v-tooltip="`Bedingungen`" href="#" :class="[buttonClass, 'bg-blue-200', 'relative']" @click.prevent="fileSettingPopup = file">
                                <div v-if="file.properties.conditions.ifs.length" class="absolute w-2 h-2 -mt-[0.05rem] -ml-[0.05rem] flex-none bg-red-900 rounded-full top-0 left-0"></div>
                                <ui-sprite src="setting" :class="[iconClass, 'text-blue-800']"></ui-sprite>
                            </a>
                        </template>
                    </f-multiplefiles>
                </div>
            </div>
            <template #actions>
                <a href="#" @click.prevent="submit">
                    <ui-sprite src="save" class="text-zinc-400 w-6 h-6"></ui-sprite>
                </a>
            </template>
        </ui-popup>

        <ui-popup v-if="fileSettingPopup !== null" :heading="`Bedingungen für Datei ${fileSettingPopup.name}`" @close="fileSettingPopup = null">
            <conditions :single="single" :value="fileSettingPopup.properties.conditions" @save="saveFileConditions"> </conditions>
        </ui-popup>

        <page-filter breakpoint="xl">
            <f-text id="search" :model-value="getFilter('search')" label="Suchen …" size="sm" @update:model-value="setFilter('search', $event)"></f-text>
            <f-switch id="past" :model-value="getFilter('past')" label="vergangene zeigen" size="sm" @update:model-value="setFilter('past', $event)"></f-switch>
            <f-switch id="inactive" :model-value="getFilter('inactive')" label="inaktive zeigen" size="sm" @update:model-value="setFilter('inactive', $event)"></f-switch>
        </page-filter>

        <table cellspacing="0" cellpadding="0" border="0" class="custom-table custom-table-sm">
            <thead>
                <th>Name</th>
                <th>Von</th>
                <th>Bis</th>
                <th>Anzahl TN</th>
                <th></th>
            </thead>

            <tr v-for="(form, index) in data" :key="index">
                <td>
                    <div v-text="form.name"></div>
                </td>
                <td>
                    <div v-text="form.from_human"></div>
                </td>
                <td>
                    <div v-text="form.to_human"></div>
                </td>
                <td>
                    <div v-text="form.participants_count"></div>
                </td>
                <td>
                    <div class="flex space-x-2">
                        <ui-action-button tooltip="Bearbeiten" class="btn-warning" icon="pencil" @click.prevent="edit(form)"></ui-action-button>
                        <ui-action-button tooltip="Teilnehmende anzeigen" class="btn-info" icon="user" @click.prevent="showParticipants(form)"></ui-action-button>
                        <ui-action-button :href="form.links.frontend" target="_BLANK" tooltip="zur Anmeldeseite" class="btn-info" icon="eye"></ui-action-button>
                        <ui-action-button :href="form.links.export" target="_BLANK" tooltip="als CSV exportieren" class="btn-info" icon="document"></ui-action-button>
                        <ui-action-button tooltip="Löschen" class="btn-danger" icon="trash" @click.prevent="deleting = form"></ui-action-button>
                    </div>
                </td>
            </tr>
        </table>
        <div class="px-6">
            <ui-pagination class="mt-4" :value="meta" @reload="reloadPage"></ui-pagination>
        </div>
    </page-layout>
</template>

<script setup>
import {ref, inject} from 'vue';
import {indexProps, useIndex} from '../../composables/useInertiaApiIndex.js';
import FormBuilder from '../formtemplate/FormBuilder.vue';
import Participants from './Participants.vue';
import Conditions from './Conditions.vue';
import {useToast} from 'vue-toastification';

const props = defineProps(indexProps);
var {meta, data, reloadPage, create, single, edit, cancel, submit, remove, getFilter, setFilter} = useIndex(props.data, 'form');
const axios = inject('axios');
const toast = useToast();

const active = ref(0);
const activeMailTab = ref(0);
const deleting = ref(null);
const showing = ref(null);
const fileSettingPopup = ref(null);

const tabs = [{title: 'Allgemeines'}, {title: 'Formular'}, {title: 'Bestätigungs-E-Mail'}, {title: 'Export'}];
const mailTabs = [{title: 'vor Daten'}, {title: 'nach Daten'}];

function setTemplate(template) {
    active.value = 0;
    single.value.config = template.config;
}

async function saveFileConditions(conditions) {
    await axios.patch(`/mediaupload/${fileSettingPopup.value.id}`, {
        properties: {
            ...fileSettingPopup.value.properties,
            conditions: conditions,
        },
    });

    fileSettingPopup.value = null;
    toast.success('Datei aktualisiert');
}

function showParticipants(form) {
    showing.value = form;
}
</script>
