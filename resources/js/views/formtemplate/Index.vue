<template>
    <page-layout>
        <template #toolbar>
            <page-toolbar-button color="primary" icon="plus" @click="create">Vorlage erstellen</page-toolbar-button>
            <page-toolbar-button :href="meta.links.form_index" color="primary" icon="event">Veranstaltungen</page-toolbar-button>
        </template>

        <ui-popup v-if="deleting !== null" :heading="`Formular-Vorlage ${deleting.name} löschen?`" @close="deleting = null">
            <div>
                <p class="mt-4">Diese Formular-Vorlage löschen?</p>
                <div class="grid grid-cols-2 gap-3 mt-6">
                    <a
                        href="#"
                        class="text-center btn btn-danger"
                        @click.prevent="
                            remove(deleting);
                            deleting = null;
                        "
                        >Formular-Vorlage löschen</a
                    >
                    <a href="#" class="text-center btn btn-primary" @click.prevent="deleting = null">Abbrechen</a>
                </div>
            </div>
        </ui-popup>

        <ui-popup v-if="single !== null" :heading="`Vorlage ${single.id ? 'bearbeiten' : 'erstellen'}`" full @close="cancel">
            <div class="flex flex-col mt-3">
                <ui-tabs v-model="activeTab" :entries="tabs"></ui-tabs>
                <form-builder v-if="activeTab === 0" v-model="single.config" :meta="meta">
                    <template #meta>
                        <f-text id="name" v-model="single.name" label="Name" required></f-text>
                    </template>
                </form-builder>
                <div v-show="activeTab === 1" class="grid gap-3">
                    <ui-note class="mt-2 col-span-full">
                        Hier kannst du die E-Mail anpassen, die nach der Anmeldung an den Teilnehmer verschickt wird.<br />
                        Es gibt dafür einen ersten E-Mail-Teil und einen zweiten E-Mail-Teil. Dazwischen werden die Daten des Teilnehmers aufgelistet.<br />
                        Die Anrede ("Hallo Max Mustermann") wird automatisch an den Anfang gesetzt.<br />
                        Außerdem kannst du Dateien hochladen, die automatisch mit angehangen werden.
                    </ui-note>
                    <ui-tabs v-model="activeMailTab" :entries="mailTabs"></ui-tabs>
                    <f-editor v-if="activeMailTab === 0" id="mail_top" v-model="single.mail_top" name="mail_top" label="E-Mail-Teil 1" :rows="8" conditions required>
                        <template #conditions="{data, resolve}">
                            <conditions-form id="mail_top_conditions" :single="single" :value="data" @save="resolve"> </conditions-form>
                        </template>
                    </f-editor>
                    <f-editor v-if="activeMailTab === 1" id="mail_bottom" v-model="single.mail_bottom" name="mail_bottom" label="E-Mail-Teil 2" :rows="8" conditions required>
                        <template #conditions="{data, resolve}">
                            <conditions-form id="mail_bottom_conditions" :single="single" :value="data" @save="resolve"> </conditions-form>
                        </template>
                    </f-editor>
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

            <tr v-for="(formtemplate, index) in data" :key="index">
                <td>
                    <div v-text="formtemplate.name"></div>
                </td>
                <td>
                    <a v-tooltip="`Bearbeiten`" href="#" class="ml-2 inline-flex btn btn-warning btn-sm" @click.prevent="edit(formtemplate)"><ui-sprite src="pencil"></ui-sprite></a>
                    <a v-tooltip="`Löschen`" href="#" class="ml-2 inline-flex btn btn-danger btn-sm" @click.prevent="deleting = formtemplate"><ui-sprite src="trash"></ui-sprite></a>
                </td>
            </tr>
        </table>
        <div class="px-6">
            <ui-pagination class="mt-4" :value="meta" @reload="reloadPage"></ui-pagination>
        </div>
    </page-layout>
</template>

<script lang="js" setup>
import { ref } from 'vue';
import { indexProps, useIndex } from '../../composables/useInertiaApiIndex.js';
import FormBuilder from './FormBuilder.vue';
import ConditionsForm from '../form/ConditionsForm.vue';

const deleting = ref(null);
const activeTab = ref(0);
const activeMailTab = ref(0);
const tabs = [{ title: 'Formular' }, { title: 'Bestätigungs-E-Mail' }];
const mailTabs = [{ title: 'vor Daten' }, { title: 'nach Daten' }];

const props = defineProps(indexProps);
var { meta, data, reloadPage, create, remove, single, edit, cancel, submit } = useIndex(props.data, 'invoice');

function innerSubmit(payload) {
    single.value = payload;

    submit();
}
</script>
