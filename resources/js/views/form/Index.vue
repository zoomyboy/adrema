<template>
    <page-layout>
        <template #toolbar>
            <page-toolbar-button :href="meta.links.formtemplate_index" color="primary"
                icon="event">Vorlagen</page-toolbar-button>
            <page-toolbar-button color="primary" icon="plus" @click.prevent="create">Veranstaltung
                erstellen</page-toolbar-button>
        </template>

        <ui-popup v-if="deleting !== null" :heading="`Veranstaltung ${deleting.name} löschen?`" @close="deleting = null">
            <div>
                <p class="mt-4">Diese Veranstaltung löschen?</p>
                <div class="grid grid-cols-2 gap-3 mt-6">
                    <a href="#" class="text-center btn btn-danger" @click.prevent="
                        remove(deleting);
                    deleting = null;
                                                ">Veranstaltung löschen</a>
                    <a href="#" class="text-center btn btn-primary" @click.prevent="deleting = null">Abbrechen</a>
                </div>
            </div>
        </ui-popup>

        <ui-popup v-if="single !== null && single.config === null" heading="Vorlage auswählen" @close="cancel">
            <div class="mt-3 grid gap-3 grid-cols-2">
                <a v-for="(template, index) in meta.templates" :key="index"
                    class="py-2 px-3 border rounded bg-zinc-800 hover:bg-zinc-700 transition" href="#"
                    @click.prevent="setTemplate(template)">
                    <span v-text="template.name"></span>
                </a>
            </div>
        </ui-popup>

        <ui-popup v-if="single !== null && single.config !== null"
            :heading="`Veranstaltung ${single.id ? 'bearbeiten' : 'erstellen'}`" full @close="cancel">
            <div class="flex flex-col mt-3">
                <ui-tabs v-model="active" :entries="tabs"></ui-tabs>
                <div v-if="active === 0" class="grid grid-cols-2 gap-3">
                    <f-text id="name" v-model="single.name" name="name" label="Name" required></f-text>
                    <f-singlefile id="header_image" v-model="single.header_image" label="Bild" name="header_image"
                        parent-name="form" :parent-id="single.id" collection="headerImage" required></f-singlefile>
                    <f-text id="from" v-model="single.from" type="date" name="from" label="Von" required></f-text>
                    <f-text id="to" v-model="single.to" type="date" name="to" label="Bis" required></f-text>
                    <f-textarea id="excerpt" v-model="single.excerpt"
                        hint="Gebe hier eine kurze Beschreibung für die Veranstaltungs-Übersicht ein (Maximal 130 Zeichen)."
                        name="excerpt" label="Auszug" rows="5" required></f-textarea>
                    <f-textarea id="description" v-model="single.description" name="description" label="Beschreibung"
                        rows="10" required></f-textarea>
                </div>
                <div v-if="active === 1">
                    <ui-note class="mt-2"> Sobald sich der erste Teilnehmer für die Veranstaltung angemeldet hat, kann
                        dieses Formular nicht mehr geändert werden. </ui-note>
                    <form-builder v-model="single.config" :meta="meta"></form-builder>
                </div>
                <div v-if="active === 2" class="grid gap-3">
                    <ui-note class="mt-2">
                        Hier kannst du die E-Mail anpassen, die nach der Anmeldung an den Teilnehmer verschickt wird.<br />
                        Es gibt dafür einen ersten E-Mail-Teil und einen zweiten E-Mail-Teil. Dazwischen werden die Daten
                        des Teilnehmers aufgelistet.<br />
                        Die Anrede ("Hallo Max Mustermann") wird automatisch an den Anfang gesetzt.</ui-note>
                    <f-textarea id="mail_top" v-model="single.mail_top" name="mail_top" label="E-Mail-Teil 1" rows="8"
                        required></f-textarea>
                    <f-textarea id="mail_bottom" v-model="single.mail_bottom" name="mail_bottom" label="E-Mail-Teil 2"
                        rows="8" required></f-textarea>
                </div>
            </div>
            <template #actions>
                <a href="#" @click.prevent="submit">
                    <ui-sprite src="save" class="text-zinc-400 w-6 h-6"></ui-sprite>
                </a>
            </template>
        </ui-popup>

        <table cellspacing="0" cellpadding="0" border="0" class="custom-table custom-table-sm">
            <thead>
                <th>Name</th>
                <th></th>
            </thead>

            <tr v-for="(form, index) in data" :key="index">
                <td>
                    <div v-text="form.name"></div>
                </td>
                <td>
                    <a v-tooltip="`Bearbeiten`" href="#" class="ml-2 inline-flex btn btn-warning btn-sm"
                        @click.prevent="edit(form)"><ui-sprite src="pencil"></ui-sprite></a>
                    <a v-tooltip="`Löschen`" href="#" class="ml-2 inline-flex btn btn-danger btn-sm"
                        @click.prevent="deleting = form"><ui-sprite src="trash"></ui-sprite></a>
                </td>
            </tr>
        </table>
        <div class="px-6">
            <ui-pagination class="mt-4" :value="meta" @reload="reloadPage"></ui-pagination>
        </div>
    </page-layout>
</template>

<script setup>
import { ref } from 'vue';
import { indexProps, useIndex } from '../../composables/useInertiaApiIndex.js';
import FormBuilder from '../formtemplate/FormBuilder.vue';

const props = defineProps(indexProps);
var { meta, data, reloadPage, create, single, edit, cancel, submit, remove } = useIndex(props.data, 'form');

const active = ref(0);
const deleting = ref(null);

const tabs = [{ title: 'Allgemeines' }, { title: 'Formular' }, { title: 'E-Mail' }, { title: 'Export' }];

function setTemplate(template) {
    active.value = 0;
    single.value.config = template.config;
}
</script>
