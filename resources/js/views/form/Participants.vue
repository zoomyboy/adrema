<template>
    <div>
        <ui-popup v-if="editing !== null" heading="Mitglied bearbeiten" closeable full @close="editing = null">
            <event-form
                :value="editing.preview"
                :base-url="meta.base_url"
                style="--primary: hsl(181, 75%, 26%); --secondary: hsl(181, 75%, 35%); --font: hsl(181, 84%, 78%); --circle: hsl(181, 86%, 16%)"
                as-form
                @save="updateParticipant($event.detail[0])"
            ></event-form>
        </ui-popup>
        <ui-popup v-if="assigning !== null" heading="Mitglied zuweisen" closeable @close="assigning = null">
            <member-assign @assign="assign"></member-assign>
        </ui-popup>
        <ui-popup v-if="deleting !== null" heading="Teilnehmer*in löschen?" @close="deleting = null">
            <div>
                <p class="mt-4">Den*Die Teilnehmer*in löschen?</p>
                <div class="grid grid-cols-2 gap-3 mt-6">
                    <a href="#" class="text-center btn btn-danger" @click.prevent="handleDelete">Mitglied loschen</a>
                    <a href="#" class="text-center btn btn-primary" @click.prevent="deleting = null">Abbrechen</a>
                </div>
            </div>
        </ui-popup>
        <page-filter breakpoint="lg">
            <template #buttons>
                <f-text id="search" v-model="innerFilter.search" name="search" label="Suchen" size="sm"></f-text>
                <ui-icon-button icon="plus" @click="editing = {participant: null, preview: JSON.stringify(meta.form_config)}">Hinzufügen</ui-icon-button>
                <f-switch v-if="meta.has_nami_field" id="group_participants" v-model="groupParticipants" label="Gruppieren" size="sm" name="group_participants"></f-switch>
                <f-multipleselect id="active_columns" v-model="activeColumnsConfig" :options="meta.columns" label="Aktive Spalten" size="sm"></f-multipleselect>
            </template>

            <template #fields>
                <template v-for="(filter, index) in meta.filters">
                    <f-select
                        v-if="filter.base_type === 'CheckboxField'"
                        :id="`filter-field-${index}`"
                        :key="`filter-field-${index}`"
                        v-model="innerFilter.data[filter.key]"
                        :null-value="meta.default_filter_value"
                        :name="`filter-field-${index}`"
                        :options="checkboxFilterOptions"
                        :label="filter.name"
                        size="sm"
                    ></f-select>
                    <f-select
                        v-if="filter.base_type === 'DropdownField'"
                        :id="`filter-field-${index}`"
                        :key="`filter-field-${index}`"
                        v-model="innerFilter.data[filter.key]"
                        :null-value="meta.default_filter_value"
                        :name="`filter-field-${index}`"
                        :options="dropdownFilterOptions(filter)"
                        :label="filter.name"
                        size="sm"
                    ></f-select>
                    <f-select
                        v-if="filter.base_type === 'RadioField'"
                        :id="`filter-field-${index}`"
                        :key="`filter-field-${index}`"
                        v-model="innerFilter.data[filter.key]"
                        :null-value="meta.default_filter_value"
                        :name="`filter-field-${index}`"
                        :options="dropdownFilterOptions(filter)"
                        :label="filter.name"
                        size="sm"
                    ></f-select>
                </template>
            </template>
        </page-filter>
        <table cellspacing="0" cellpadding="0" border="0" class="custom-table custom-table-sm">
            <thead>
                <th v-for="column in activeColumns" :key="column.id" v-text="column.name"></th>
                <th></th>
            </thead>

            <template v-for="(participant, index) in data" :key="index">
                <tr>
                    <td v-for="(column, columnindex) in activeColumns" :key="column.id">
                        <div class="flex items-center space-x-2">
                            <button v-if="columnindex === 0 && participant.member_id === null" v-tooltip="`kein Mitglied zugewiesen. Per Klick zuweisen`" @click.prevent="assigning = participant">
                                <ui-sprite src="warning-triangle" class="text-yellow-400 w-5 h-5"></ui-sprite>
                            </button>
                            <ui-table-toggle-button v-if="columnindex === 0 && groupParticipants" :value="participant" :level="0" :active="isOpen(participant.id)" @toggle="toggle(participant)">
                                <prevention v-if="column.display_attribute === 'prevention_display'" :value="participant.prevention_items"></prevention>
                                <span v-else v-text="participant[column.display_attribute]"></span>
                            </ui-table-toggle-button>
                            <div v-else>
                                <prevention v-if="column.display_attribute === 'prevention_display'" :value="participant.prevention_items"></prevention>
                                <span v-else v-text="participant[column.display_attribute]"></span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <a v-tooltip="`Bearbeiten`" href="#" class="ml-2 inline-flex btn btn-warning btn-sm" @click.prevent="editReal(participant)"><ui-sprite src="pencil"></ui-sprite></a>
                        <a v-tooltip="`Löschen`" href="#" class="ml-2 inline-flex btn btn-danger btn-sm" @click.prevent="deleting = participant"><ui-sprite src="trash"></ui-sprite></a>
                    </td>
                </tr>
                <template v-for="child in childrenOf(participant.id)" :key="child.id">
                    <tr>
                        <td v-for="(column, columnindex) in activeColumns" :key="column.id">
                            <div class="flex items-center space-x-2">
                                <ui-table-toggle-button v-if="columnindex === 0 && groupParticipants" :value="child" :level="1" :active="isOpen(child.id)" @toggle="toggle(child)">
                                    <prevention v-if="column.display_attribute === 'prevention_display'" :value="child.prevention_items"></prevention>
                                    <span v-else v-text="child[column.display_attribute]"></span>
                                </ui-table-toggle-button>
                                <div v-else>
                                    <prevention v-if="column.display_attribute === 'prevention_display'" :value="child.prevention_items"></prevention>
                                    <span v-else v-text="child[column.display_attribute]"></span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <a v-tooltip="`Bearbeiten`" href="#" class="ml-2 inline-flex btn btn-warning btn-sm" @click.prevent="editReal(child)"><ui-sprite src="pencil"></ui-sprite></a>
                            <a v-tooltip="`Löschen`" href="#" class="ml-2 inline-flex btn btn-danger btn-sm" @click.prevent="deleting = child"><ui-sprite src="trash"></ui-sprite></a>
                        </td>
                    </tr>
                </template>
            </template>
        </table>
        <div class="px-6">
            <ui-pagination class="mt-4" :value="meta" @reload="reloadPage($event, {filter: toFilterString(innerFilter)})"></ui-pagination>
        </div>
    </div>
</template>

<script lang="js" setup>
import { watch, ref, computed } from 'vue';
import { useApiIndex } from '../../composables/useApiIndex.js';
import useTableToggle from '../../composables/useTableToggle.js';
import MemberAssign from './MemberAssign.vue';
import Prevention from './Prevention.vue';

const deleting = ref(null);
const { isOpen, toggle, childrenOf, clearToggle } = useTableToggle({});

const assigning = ref(null);

const props = defineProps({
    url: {
        type: String,
        required: true,
        validator: (value) => value.startsWith('http'),
    },
    rootUrl: {
        type: String,
        required: true,
        validator: (value) => value.startsWith('http'),
    },
    hasNamiField: {
        type: Boolean,
        required: true,
    },
});
const groupParticipants = computed({
    get() {
        return url.value === props.rootUrl;
    },
    async set(v) {
        updateUrl(v ? props.rootUrl : props.url);
        if (!v) {
            clearToggle();
        }
        await reload();
    },
});

async function assign(memberId) {
    await axios.post(assigning.value.links.assign, { member_id: memberId });
    reload(false);
    assigning.value = null;
}

var { meta, data, reload, reloadPage, axios, remove, toFilterString, url, updateUrl } = useApiIndex(props.hasNamiField ? props.rootUrl : props.url, 'participant');

const activeColumns = computed(() => meta.value.columns.filter((c) => meta.value.form_meta.active_columns.includes(c.id)));

const activeColumnsConfig = computed({
    get: () => meta.value.form_meta.active_columns,
    set: async (v) => {
        const response = await axios.patch(meta.value.links.update_form_meta, {
            ...meta.value.form_meta,
            active_columns: v,
        });

        meta.value.form_meta = response.data;
    },
});

async function handleDelete() {
    await remove(deleting.value);
    deleting.value = null;
}

await reload();

const innerFilter = ref(JSON.parse(JSON.stringify(meta.value.filter)));

watch(
    innerFilter,
    async function (newValue) {
        await reload(true, {
            filter: toFilterString(newValue),
        });
    },
    { deep: true }
);

const checkboxFilterOptions = ref([
    { id: true, name: 'Ja' },
    { id: false, name: 'Nein' },
]);

function dropdownFilterOptions(filter) {
    return [
        { id: null, name: 'keine Auswahl' },
        ...filter.options.map((f) => {
            return { id: f, name: f };
        }),
    ];
}

async function editReal(participant) {
    const response = await axios.get(participant.links.fields);
    editing.value = {
        participant: participant,
        preview: JSON.stringify(response.data.data.config),
    };
}

async function updateParticipant(payload) {
    if (editing.value.participant === null) {
        await axios.post(meta.value.links.store_participant, payload);
    } else {
        await axios.patch(editing.value.participant.links.update, payload);
    }

    await reload();

    editing.value = null;
}

const editing = ref(null);
</script>
