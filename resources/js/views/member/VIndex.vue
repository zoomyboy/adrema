<template>
    <page-layout page-class="pb-6">
        <template #toolbar>
            <page-toolbar-button :href="meta.links.create" color="primary" icon="plus">Mitglied
                anlegen</page-toolbar-button>
            <page-toolbar-button v-if="hasModule('bill')" :href="meta.links.allpayment" color="primary"
                icon="invoice">Rechnungen erstellen</page-toolbar-button>
            <page-toolbar-button v-if="hasModule('bill')" :href="meta.links.sendpayment" color="info"
                icon="envelope">Rechnungen versenden</page-toolbar-button>
        </template>
        <ui-popup v-if="deleting !== null" heading="Mitglied löschen?" @close="deleting.reject()">
            <div>
                <p class="mt-4">Das Mitglied "{{ deleting.member.fullname }}" löschen?</p>
                <p class="mt-2">Alle Zuordnungen (Ausbildungen, Rechnungen, Zahlungen, Tätigkeiten) werden ebenfalls
                    entfernt.</p>
                <ui-note v-if="!deleting.member.has_nami" class="mt-5" type="warning"> Dieses Mitglied ist nicht in NaMi
                    vorhanden und wird daher nur in der AdReMa gelöscht werden. </ui-note>
                <ui-note v-if="deleting.member.has_nami" class="mt-5" type="danger">
                    Dieses Mitglied ist in NaMi vorhanden und wird daher in NaMi abgemeldet werden. Sofern
                    "Datenweiterverwendung" eingeschaltet ist, wird das Mitglied auf inaktiv gesetzt.
                </ui-note>
                <div class="grid grid-cols-2 gap-3 mt-6">
                    <a href="#" class="text-center btn btn-danger" @click.prevent="deleting.resolve">Mitglied loschen</a>
                    <a href="#" class="text-center btn btn-primary" @click.prevent="deleting.reject">Abbrechen</a>
                </div>
            </div>
        </ui-popup>
        <div class="px-6 py-2 flex border-b border-gray-600 items-center space-x-3">
            <f-text id="search" :model-value="getFilter('search')" name="search" label="Suchen …" size="sm"
                @update:model-value="setFilter('search', $event)"></f-text>
            <f-switch v-show="hasModule('bill')" id="ausstand" :model-value="getFilter('ausstand')" label="Nur Ausstände"
                size="sm" @update:model-value="setFilter('ausstand', $event)"></f-switch>
            <f-multipleselect id="group_ids" :options="meta.groups" :model-value="getFilter('group_ids')"
                label="Gruppierungen" size="sm" name="group_ids"
                @update:model-value="setFilter('group_ids', $event)"></f-multipleselect>
            <f-select v-show="hasModule('bill')" id="billKinds" name="billKinds" :options="meta.billKinds"
                :model-value="getFilter('bill_kind')" label="Rechnung" size="sm"
                @update:model-value="setFilter('bill_kind', $event)"></f-select>
            <f-multipleselect id="activity_ids" :options="meta.filterActivities" :model-value="getFilter('activity_ids')"
                label="Tätigkeiten" size="sm" name="activity_ids"
                @update:model-value="setFilter('activity_ids', $event)"></f-multipleselect>
            <f-multipleselect id="subactivity_ids" :options="meta.filterSubactivities"
                :model-value="getFilter('subactivity_ids')" label="Untertätigkeiten" size="sm" name="subactivity_ids"
                @update:model-value="setFilter('subactivity_ids', $event)"></f-multipleselect>
            <button class="btn btn-primary label mr-2" @click.prevent="exportMembers">
                <ui-sprite class="w-3 h-3 xl:mr-2" src="save"></ui-sprite>
                <span class="hidden xl:inline">Exportieren</span>
            </button>
        </div>

        <table cellspacing="0" cellpadding="0" border="0" class="custom-table custom-table-sm hidden md:table">
            <thead>
                <th></th>
                <th>Nachname</th>
                <th>Vorname</th>
                <th class="hidden 2xl:table-cell">Ort</th>
                <th>Tags</th>
                <th class="hidden xl:table-cell">Alter</th>
                <th v-show="hasModule('bill')" class="hidden xl:table-cell">Rechnung</th>
                <th v-show="hasModule('bill')">Ausstand</th>
                <th></th>
            </thead>

            <tr v-for="(member, index) in data" :key="index">
                <td><ui-age-groups :member="member"></ui-age-groups></td>
                <td v-text="member.lastname"></td>
                <td v-text="member.firstname"></td>
                <td class="hidden 2xl:table-cell" v-text="member.full_address"></td>
                <td>
                    <tags :member="member"></tags>
                </td>
                <td class="hidden xl:table-cell" v-text="member.age"></td>
                <td v-show="hasModule('bill')" class="hidden xl:table-cell">
                    <ui-label :value="member.bill_kind_name" fallback="kein"></ui-label>
                </td>
                <td v-show="hasModule('bill')">
                    <ui-label :value="member.pending_payment" fallback="---"></ui-label>
                </td>
                <td>
                    <actions :member="member" @sidebar="openSidebar(index, $event)" @remove="remove(member)"></actions>
                </td>
            </tr>
        </table>

        <div class="md:hidden p-3 grid gap-3">
            <ui-box v-for="(member, index) in data.data" :key="index" class="relative" :heading="member.fullname">
                <template #in-title>
                    <ui-age-groups class="ml-2" :member="member" icon-class="w-4 h-4"></ui-age-groups>
                </template>
                <div class="text-xs text-gray-200" v-text="member.full_address"></div>
                <div class="flex items-center mt-1 space-x-4">
                    <tags :member="member"></tags>
                    <ui-label v-show="hasModule('bill')" class="text-gray-100 block" :value="member.pending_payment"
                        fallback=""></ui-label>
                </div>
                <actions class="mt-2" :member="member" @sidebar="openSidebar(index, $event)" @remove="remove(member)">
                </actions>
                <div class="absolute right-0 top-0 h-full flex items-center mr-2">
                    <i-link v-tooltip="`Details`" :href="member.links.show"><ui-sprite src="chevron-down"
                            class="w-6 h-6 text-teal-100 -rotate-90"></ui-sprite></i-link>
                </div>
            </ui-box>
        </div>

        <div class="px-6">
            <ui-pagination class="mt-4" :value="meta" :only="['data']"></ui-pagination>
        </div>

        <member-payments v-if="single !== null && sidebar === 'payment.index'" :subscriptions="meta.subscriptions"
            :statuses="meta.statuses" :value="data[single]" @close="closeSidebar"></member-payments>
        <member-memberships v-if="single !== null && sidebar === 'membership.index'" :groups="meta.groups"
            :activities="meta.formActivities" :subactivities="meta.formSubactivities" :value="data[single]"
            @close="closeSidebar"></member-memberships>
        <member-courses v-if="single !== null && sidebar === 'courses.index'" :courses="meta.courses" :value="data[single]"
            @close="closeSidebar"></member-courses>
    </page-layout>
</template>

<script setup>
import MemberPayments from './MemberPayments.vue';
import MemberMemberships from './MemberMemberships.vue';
import MemberCourses from './MemberCourses.vue';
import Tags from './Tags.vue';
import Actions from './index/Actions.vue';
import { indexProps, useIndex } from '../../composables/useIndex.js';
import { ref, defineProps } from 'vue';

const sidebar = ref(null);
const single = ref(null);
const deleting = ref(null);

const props = defineProps(indexProps);
var { router, data, meta, getFilter, setFilter, filterString } = useIndex(props.data, 'member');

function exportMembers() {
    window.open(`/member-export?filter=${filterString.value}`);
}

async function remove(member) {
    new Promise((resolve, reject) => {
        deleting.value = { resolve, reject, member };
    })
        .then(() => {
            router.delete(`/member/${member.id}`);
            deleting.value = null;
        })
        .catch(() => (deleting.value = null));
}

function openSidebar(index, name) {
    single.value = index;
    sidebar.value = name;
}
function closeSidebar() {
    single.value = null;
    sidebar.value = null;
}
</script>
