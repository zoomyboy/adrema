<template>
    <page-layout>
        <template #toolbar>
            <page-toolbar-button color="primary" icon="plus" @click="create">Rechnung anlegen</page-toolbar-button>
            <page-toolbar-button color="primary" icon="plus" @click="massstore = {year: ''}">Massenrechnung anlegen</page-toolbar-button>
            <page-toolbar-button :href="meta.links.masspdf" color="primary" icon="plus" as-a>Post-Briefe abrufen</page-toolbar-button>
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
        <ui-popup v-if="deleting !== null" heading="Rechnung löschen?" @close="deleting = null">
            <div>
                <p class="mt-4">Diese Rechnung löschen?</p>
                <div class="grid grid-cols-2 gap-3 mt-6">
                    <a
                        href="#"
                        class="text-center btn btn-danger"
                        @click.prevent="
                            remove(deleting);
                            deleting = null;
                        "
                        >Rechnung löschen</a
                    >
                    <a href="#" class="text-center btn btn-primary" @click.prevent="deleting = null">Abbrechen</a>
                </div>
            </div>
        </ui-popup>
        <ui-popup v-if="single !== null" :heading="`Rechnung ${single.id ? 'bearbeiten' : 'erstellen'}`" inner-width="max-w-4xl" @close="cancel">
            <form class="grid grid-cols-2 gap-3 mt-4" @submit.prevent="submit">
                <ui-box heading="Empfänger" container-class="grid grid-cols-2 gap-3 col-span-full">
                    <f-text id="to_name" v-model="single.to.name" name="to_name" label="Name" class="col-span-full" required></f-text>
                    <f-text id="to_address" v-model="single.to.address" name="to_address" label="Adresse" class="col-span-full" required></f-text>
                    <f-text id="to_zip" v-model="single.to.zip" name="to_zip" label="PLZ" required></f-text>
                    <f-text id="to_location" v-model="single.to.location" name="to_location" label="Ort" required></f-text>
                    <f-text id="mail_email" v-model="single.mail_email" name="mail_email" label="E-Mail-Adresse" class="col-span-full"></f-text>
                </ui-box>
                <ui-box heading="Status" container-class="grid gap-3">
                    <f-select id="status" v-model="single.status" :options="meta.statuses" name="status" label="Status" required></f-select>
                    <f-select id="via" v-model="single.via" :options="meta.vias" name="via" label="Rechnungsweg" required></f-select>
                    <f-text id="greeting" v-model="single.greeting" name="greeting" label="Anrede" required></f-text>
                    <f-text id="usage" v-model="single.usage" name="usage" label="Verwendungszweck" required></f-text>
                </ui-box>
                <ui-box heading="Positionen" class="col-span-full" container-class="grid gap-3">
                    <template #in-title>
                        <ui-icon-button class="ml-3 btn-primary" icon="plus" @click="single.positions.push({...meta.default_position})">Neu</ui-icon-button>
                    </template>
                    <div v-for="(position, index) in single.positions" :key="index" class="flex items-end space-x-3">
                        <f-text :id="`position-description-${index}`" v-model="position.description" class="grow" :name="`position-description-${index}`" label="Beschreibung" required></f-text>
                        <f-text :id="`position-price-${index}`" v-model="position.price" mode="area" :name="`position-price-${index}`" label="Preis" required></f-text>
                        <f-select :id="`position-member-${index}`" v-model="position.member_id" :options="meta.members" :name="`position-member-${index}`" label="Mitglied" required></f-select>
                        <button type="button" class="btn btn-danger btn-sm h-[35px]" icon="trash" @click="single.positions.splice(index, 1)"><ui-sprite src="trash"></ui-sprite></button>
                    </div>
                </ui-box>
                <section class="flex mt-4 space-x-2">
                    <ui-button type="submit" class="btn-danger">Speichern</ui-button>
                    <ui-button class="btn-primary" @click.prevent="cancel">Abbrechen</ui-button>
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
                    <div v-text="invoice.to.name"></div>
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
                <td>
                    <a v-tooltip="`Anschauen`" :href="invoice.links.pdf" target="_BLANK" class="inline-flex btn btn-info btn-sm"><ui-sprite src="eye"></ui-sprite></a>
                    <a v-tooltip="`Erinnerung anschauen`" :href="invoice.links.rememberpdf" target="_BLANK" class="ml-2 inline-flex btn btn-info btn-sm"><ui-sprite src="document"></ui-sprite></a>
                    <a v-tooltip="`Bearbeiten`" href="#" class="ml-2 inline-flex btn btn-warning btn-sm" @click.prevent="edit(invoice)"><ui-sprite src="pencil"></ui-sprite></a>
                    <a v-tooltip="`Löschen`" href="#" class="ml-2 inline-flex btn btn-danger btn-sm" @click.prevent="deleting = invoice"><ui-sprite src="trash"></ui-sprite></a>
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
const props = defineProps(indexProps);
var {axios, meta, data, reloadPage, create, single, edit, cancel, submit, remove} = useIndex(props.data, 'invoice');
const massstore = ref(null);
const deleting = ref(null);

async function sendMassstore() {
    await axios.post(meta.value.links['mass-store'], massstore.value);
    massstore.value = null;
}
</script>
