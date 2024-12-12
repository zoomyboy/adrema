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
                    <f-text id="year" v-model="massstore.year" label="Jahr" required></f-text>
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
                <ui-box heading="Für Mitglied anlegen" container-class="flex space-x-3" class="col-span-full">
                    <f-select id="forMemberMember" v-model="forMember.member_id" name="forMemberMember" :options="meta.members" label="Mitglied"></f-select>
                    <f-select id="forMemberSubscription" v-model="forMember.subscription_id" name="forMemberSubscription" :options="meta.subscriptions" label="Beitrag"></f-select>
                    <f-text id="forMemberYear" v-model="forMember.year" label="Jahr"></f-text>
                    <ui-icon-button class="btn-primary self-end mb-2" icon="save" @click="saveForMember">Speichern</ui-icon-button>
                </ui-box>
                <ui-box heading=" Empfänger" container-class="grid grid-cols-2 gap-3 col-span-full">
                    <f-text id="to_name" v-model="single.to.name" label="Name" class="col-span-full" required></f-text>
                    <f-text id="to_address" v-model="single.to.address" label="Adresse" class="col-span-full" required></f-text>
                    <f-text id="to_zip" v-model="single.to.zip" label="PLZ" required></f-text>
                    <f-text id="to_location" v-model="single.to.location" label="Ort" required></f-text>
                    <f-text id="mail_email" v-model="single.mail_email" label="E-Mail-Adresse" class="col-span-full"></f-text>
                </ui-box>
                <ui-box heading="Status" container-class="grid gap-3">
                    <f-select id="status" v-model="single.status" :options="meta.statuses" name="status" label="Status" required></f-select>
                    <f-select id="via" v-model="single.via" :options="meta.vias" name="via" label="Rechnungsweg" required></f-select>
                    <f-text id="greeting" v-model="single.greeting" label="Anrede" required></f-text>
                    <f-text id="usage" v-model="single.usage" label="Verwendungszweck" required></f-text>
                </ui-box>
                <ui-box heading="Positionen" class="col-span-full" container-class="grid gap-3">
                    <template #in-title>
                        <ui-icon-button class="ml-3 btn-primary" icon="plus" @click="single.positions.push({...meta.default_position})">Neu</ui-icon-button>
                    </template>
                    <div v-for="(position, index) in single.positions" :key="index" class="flex items-end space-x-3">
                        <f-text :id="`position-description-${index}`" v-model="position.description" class="grow" label="Beschreibung" required></f-text>
                        <f-text :id="`position-price-${index}`" v-model="position.price" mode="area" label="Preis" required></f-text>
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
        <page-filter breakpoint="xl" :filterable="false">
            <template #buttons>
                <f-multipleselect
                    id="statuses"
                    :options="meta.statuses"
                    :model-value="getFilter('statuses')"
                    label="Status"
                    size="sm"
                    @update:model-value="setFilter('statuses', $event)"
                ></f-multipleselect>
            </template>
        </page-filter>
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
                    <div class="flex space-x-2">
                        <ui-action-button tooltip="Anschauen" :href="invoice.links.pdf" class="btn-info" icon="eye" blank></ui-action-button>
                        <ui-action-button tooltip="Erinnerung anschauen" :href="invoice.links.rememberpdf" class="btn-info" icon="document" blank></ui-action-button>
                        <ui-action-button tooltip="Als Bezahlt markieren" class="btn-warning" icon="money" blank @click.prevent="markAsPaid(invoice)"></ui-action-button>
                        <ui-action-button :data-cy="`edit-button-${invoice.id}`" tooltip="Bearbeiten" class="btn-warning" icon="pencil" @click.prevent="edit(invoice)"></ui-action-button>
                        <ui-action-button tooltip="Löschen" class="btn-danger" icon="trash" @click.prevent="deleting = invoice"></ui-action-button>
                    </div>
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
const props = defineProps(indexProps);
var { axios, meta, data, reloadPage, create, single, edit, cancel, submit, remove, getFilter, setFilter } = useIndex(props.data, 'invoice');
const massstore = ref(null);
const deleting = ref(null);
const forMember = ref({ member_id: null, subscription_id: null, year: null });

async function saveForMember() {
    single.value = (await axios.post(meta.value.links.newInvoiceAttributes, forMember.value)).data;
}

async function sendMassstore() {
    await axios.post(meta.value.links['mass-store'], massstore.value);
    massstore.value = null;
}

async function markAsPaid(invoice) {
    await axios.patch(invoice.links.update, {...invoice, status: 'Rechnung beglichen'});
    await reloadPage();
}
</script>
