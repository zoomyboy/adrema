<template>
    <page-layout page-class="pb-6">
        <template #toolbar>
            <page-toolbar-button :href="meta.links.create" color="primary" icon="plus">Mitglied anlegen</page-toolbar-button>
        </template>
        <ui-popup v-if="deleting !== null" heading="Mitglied löschen?" @close="deleting.reject()">
            <div>
                <p class="mt-4">Das Mitglied "{{ deleting.member.fullname }}" löschen?</p>
                <p class="mt-2">Alle Zuordnungen (Ausbildungen, Rechnungen, Zahlungen, Tätigkeiten) werden ebenfalls entfernt.</p>
                <ui-note v-if="!deleting.member.has_nami" class="mt-5" type="warning"> Dieses Mitglied ist nicht in NaMi vorhanden und wird daher nur in der AdReMa gelöscht werden. </ui-note>
                <ui-note v-if="deleting.member.has_nami" class="mt-5" type="danger">
                    Dieses Mitglied ist in NaMi vorhanden und wird daher in NaMi abgemeldet werden. Sofern "Datenweiterverwendung" eingeschaltet ist, wird das Mitglied auf inaktiv gesetzt.
                </ui-note>
                <div class="grid grid-cols-2 gap-3 mt-6">
                    <a href="#" class="text-center btn btn-danger" @click.prevent="deleting.resolve">Mitglied loschen</a>
                    <a href="#" class="text-center btn btn-primary" @click.prevent="deleting.reject">Abbrechen</a>
                </div>
            </div>
        </ui-popup>
        <page-filter>
            <template #fields>
                <member-filter-fields :model-value="filter" @update:model-value="setFilterObject($event)" />
            </template>
            <template #buttons>
                <f-text id="search" :model-value="filter.search" label="Suchen …" size="sm" @update:model-value="setFilterObject({...filter, search: $event})" />
                <button class="btn btn-primary label mr-2" @click.prevent="exportMembers">
                    <ui-sprite class="w-3 h-3 xl:mr-2" src="save" />
                    <span class="hidden xl:inline">Exportieren</span>
                </button>
            </template>
        </page-filter>

        <table cellspacing="0" cellpadding="0" border="0" class="custom-table custom-table-sm hidden md:table">
            <thead>
                <th />
                <th>Nachname</th>
                <th>Vorname</th>
                <th class="!hidden 2xl:!table-cell">Ort</th>
                <th>Tags</th>
                <th class="!hidden xl:!table-cell">Alter</th>
                <th v-if="hasModule('bill')" class="!hidden xl:!table-cell">Rechnung</th>
                <th v-if="hasModule('bill')">Ausstand</th>
                <th />
            </thead>

            <tr v-for="(member, index) in data" :key="index">
                <td><ui-age-groups :member="member" /></td>
                <td v-text="member.lastname" />
                <td v-text="member.firstname" />
                <td class="!hidden 2xl:!table-cell" v-text="member.full_address" />
                <td>
                    <tags :member="member" />
                </td>
                <td class="!hidden xl:!table-cell" v-text="member.age" />
                <td v-if="hasModule('bill')" class="!hidden xl:!table-cell">
                    <ui-label :value="member.bill_kind_name" fallback="kein" />
                </td>
                <td v-if="hasModule('bill')">
                    <ui-label :value="member.pending_payment" fallback="---" />
                </td>
                <td>
                    <actions :member="member" @sidebar="openSidebar($event, member)" @remove="remove(member)" />
                </td>
            </tr>
        </table>

        <div class="md:hidden p-3 grid gap-3">
            <ui-box v-for="(member, index) in data" :key="index" class="relative" :heading="member.fullname">
                <template #in-title>
                    <ui-age-groups class="ml-2" :member="member" icon-class="w-4 h-4" />
                </template>
                <div class="text-xs text-gray-200" v-text="member.full_address" />
                <div class="flex items-center mt-1 space-x-4">
                    <tags :member="member" />
                    <ui-label v-show="hasModule('bill')" class="text-gray-100 block" :value="member.pending_payment" fallback="" />
                </div>
                <actions class="mt-2" :member="member" @sidebar="openSidebar($event, member)" @remove="remove(member)" />
                <div class="absolute right-0 top-0 h-full flex items-center mr-2">
                    <i-link v-tooltip="`Details`" :href="member.links.show"><ui-sprite src="chevron" class="w-6 h-6 text-teal-100 -rotate-90" /></i-link>
                </div>
            </ui-box>
        </div>

        <div class="px-6">
            <ui-pagination class="mt-4" :value="meta" @reload="reloadPage" />
        </div>

        <ui-sidebar v-if="single !== null" @close="closeSidebar">
            <member-invoice-positions v-if="single.type === 'invoicePosition'" :url="single.model.links.invoiceposition_index" @close="closeSidebar" />
            <member-memberships v-if="single.type === 'membership'" :url="single.model.links.membership_index" @close="closeSidebar" />
            <member-courses v-if="single.type === 'courses'" :url="single.model.links.course_index" @close="closeSidebar" />
        </ui-sidebar>
    </page-layout>
</template>

<script setup>
import MemberInvoicePositions from './MemberInvoicePositions.vue';
import MemberMemberships from './MemberMemberships.vue';
import MemberCourses from './MemberCourses.vue';
import Tags from './Tags.vue';
import Actions from './index/Actions.vue';
import {indexProps, useIndex} from '../../composables/useIndex.js';
import {ref, defineProps} from 'vue';
import MemberFilterFields from './MemberFilterFields.vue';

const single = ref(null);
const deleting = ref(null);

const props = defineProps(indexProps);
const {router, data, meta, filter, setFilterObject, filterString, reloadPage} = useIndex(props.data, 'member');

function exportMembers() {
    window.open(`/member-export?filter=${filterString.value}`);
}

async function remove(member) {
    new Promise((resolve, reject) => {
        deleting.value = {resolve, reject, member};
    })
        .then(() => {
            router.delete(`/member/${member.id}`);
            deleting.value = null;
        })
        .catch(() => (deleting.value = null));
}

function openSidebar(type, model) {
    single.value = {
        type: type,
        model: model,
    };
}
function closeSidebar() {
    single.value = null;
}
</script>
