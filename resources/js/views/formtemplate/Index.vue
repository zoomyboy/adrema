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
            <form-builder v-model="single.config" :meta="meta">
                <template #meta>
                    <f-text id="name" v-model="single.name" label="Name" required></f-text>
                </template>
            </form-builder>
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

<script setup>
import {ref} from 'vue';
import {indexProps, useIndex} from '../../composables/useInertiaApiIndex.js';
import FormBuilder from './FormBuilder.vue';

const deleting = ref(null);

const props = defineProps(indexProps);
var {meta, data, reloadPage, create, remove, single, edit, cancel, submit} = useIndex(props.data, 'invoice');

function innerSubmit(payload) {
    single.value = payload;

    submit();
}
</script>
