<template>
    <ui-note v-if="locked" class="mt-2" type="danger">
        Dieses Formular wurde bereits bearbeitet.<br />
        Bitte speichere es erst ab und editiere dann die Bedingungen.
    </ui-note>

    <div v-else>
        <f-select id="mode" v-model="inner.mode" :options="modeOptions" name="mode" label="Modus"></f-select>

        <ui-icon-button class="mt-4 mb-2" icon="plus" @click="addCondition">Bedingung einfügen</ui-icon-button>

        <div v-for="(condition, index) in inner.ifs" :key="index" class="grid grid-cols-[1fr_1fr_1fr_max-content] gap-2">
            <f-select
                :id="`field-${index}`"
                :model-value="condition.field"
                :options="fieldOptions"
                :name="`field-${index}`"
                label="Feld"
                @update:model-value="update(index, 'field', $event)"
            ></f-select>
            <f-select
                :id="`comparator-${index}`"
                :options="comparatorOptions"
                :model-value="condition.comparator"
                :name="`comparator-${index}`"
                label="Vergleich"
                @update:model-value="update(index, 'comparator', $event)"
            ></f-select>
            <f-select
                v-if="condition.field && ['isEqual', 'isNotEqual'].includes(condition.comparator) && ['RadioField', 'DropdownField'].includes(getField(condition.field).type)"
                :id="`value-${index}`"
                v-model="condition.value"
                :options="getOptions(condition.field)"
                :name="`value-${index}`"
                label="Wert"
            ></f-select>
            <f-multipleselect
                v-if="condition.field && ['isIn', 'isNotIn'].includes(condition.comparator) && ['RadioField', 'DropdownField'].includes(getField(condition.field).type)"
                :id="`value-${index}`"
                v-model="condition.value"
                :options="getOptions(condition.field)"
                label="Wert"
            ></f-multipleselect>
            <f-switch
                v-if="condition.field && condition.comparator && ['CheckboxField'].includes(getField(condition.field).type)"
                :id="`value-${index}`"
                v-model="condition.value"
                :name="`value-${index}`"
                label="Wert"
            ></f-switch>
            <ui-action-button tooltip="Löschen" icon="trash" class="btn-danger self-end h-8" @click="inner.ifs.splice(index, 1)"></ui-action-button>
        </div>

        <ui-icon-button class="mt-4 mb-2" icon="save" @click="save">Speichern</ui-icon-button>
    </div>
</template>

<script setup>
import {ref, inject, computed} from 'vue';
const axios = inject('axios');
const emit = defineEmits(['save']);

const props = defineProps({
    value: {
        required: true,
    },
    single: {
        required: true,
    },
});

const comparatorOptions = ref([
    {id: 'isEqual', name: 'ist gleich', defaultValue: {DropdownField: null, RadioField: null, CheckboxField: false}},
    {id: 'isNotEqual', name: 'ist ungleich', defaultValue: {DropdownField: null, RadioField: null, CheckboxField: false}},
    {id: 'isIn', name: 'ist in', defaultValue: {DropdownField: [], RadioField: [], CheckboxField: false}},
    {id: 'isNotIn', name: 'ist nicht in', defaultValue: {DropdownField: [], RadioField: [], CheckboxField: false}},
]);

const modeOptions = ref([
    {id: 'all', name: 'alle Bedingungen müssen zutreffen'},
    {id: 'any', name: 'mindestens eine Bedingung muss zutreffen'},
]);

const fields = computed(() => {
    const result = [];
    props.single.config.sections.forEach((section) => {
        section.fields.forEach((field) => {
            if (['DropdownField', 'RadioField', 'CheckboxField'].includes(field.type)) {
                result.push(field);
            }
        });
    });

    return result;
});

function update(index, key, value) {
    if (key === 'comparator') {
        var old = inner.value.ifs[index];
        inner.value.ifs[index] = {
            field: old.field,
            comparator: value,
            value: old.field ? comparatorOptions.value.find((c) => c.id === value).defaultValue[getField(old.field).type] : null,
        };
    }
    if (key === 'field') {
        var old = inner.value.ifs[index];
        inner.value.ifs[index] = {
            field: value,
            comparator: null,
            value: null,
        };
    }
}

function getField(fieldName) {
    return fields.value.find((f) => f.key === fieldName);
}

function getOptions(fieldName) {
    return getField(fieldName).options.map((o) => {
        return {id: o, name: o};
    });
}

const fieldOptions = computed(() =>
    fields.value.map((field) => {
        return {id: field.key, name: field.name};
    })
);

const inner = ref(JSON.parse(JSON.stringify(props.value)));

const locked = ref(false);

function addCondition() {
    inner.value.ifs.push({
        field: null,
        comparator: null,
        value: null,
    });
}

async function save() {
    emit('save', inner.value);
}

async function checkIfDirty() {
    const response = await axios.post(props.single.links.is_dirty, {config: props.single.config});

    locked.value = response.data.result;
}

checkIfDirty();
</script>
