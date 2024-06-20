<template>
    <div class="mt-5">
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
            <f-multipleselect id="active_columns" v-model="activeColumnsConfig" :options="meta.columns" label="Aktive Spalten" size="sm" name="active_columns"></f-multipleselect>

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
        </page-filter>
        <table cellspacing="0" cellpadding="0" border="0" class="custom-table custom-table-sm">
            <thead>
                <th v-for="column in activeColumns" :key="column.id" v-text="column.name"></th>
                <th></th>
            </thead>

            <tr v-for="(participant, index) in data" :key="index">
                <td v-for="column in activeColumns" :key="column.id">
                    <div v-text="participant[column.display_attribute]"></div>
                </td>
                <td>
                    <a v-tooltip="`Bearbeiten`" href="#" class="ml-2 inline-flex btn btn-warning btn-sm" @click.prevent="edit(participant)"><ui-sprite src="pencil"></ui-sprite></a>
                    <a v-tooltip="`Löschen`" href="#" class="ml-2 inline-flex btn btn-danger btn-sm" @click.prevent="deleting = participant"><ui-sprite src="trash"></ui-sprite></a>
                </td>
            </tr>
        </table>
        <div class="px-6">
            <ui-pagination class="mt-4" :value="meta" @reload="reloadPage"></ui-pagination>
        </div>
    </div>
</template>

<script setup>
import {watch, ref, computed} from 'vue';
import {useApiIndex} from '../../composables/useApiIndex.js';

const deleting = ref(null);

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

var {meta, data, reload, reloadPage, axios, remove, toFilterString} = useApiIndex(props.url, 'participant');

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
    {deep: true}
);

const checkboxFilterOptions = ref([
    {id: true, name: 'Ja'},
    {id: false, name: 'Nein'},
]);

function dropdownFilterOptions(filter) {
    return [
        {id: null, name: 'keine Auswahl'},
        ...filter.options.map((f) => {
            return {id: f, name: f};
        }),
    ];
}
</script>
