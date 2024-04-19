<template>
    <ui-note v-if="locked" class="mt-2" type="danger">
        Dieses Formular wurde bereits bearbeitet.<br />
        Bitte speichere es erst ab und editiere dann die Bedingungen.
    </ui-note>

    <div v-else>
        <f-select id="mode" v-model="inner.mode" :options="modeOptions" name="mode" label="Modus"></f-select>

        <ui-icon-button class="mt-4 mb-2" icon="plus" @click="addCondition">Bedingung einfügen</ui-icon-button>

        <div v-for="(condition, index) in inner.ifs" :key="index" class="grid grid-cols-[1fr_1fr_1fr_max-content] gap-2">
            <f-select :id="`field-${index}`" v-model="condition.field" :options="fieldOptions" :name="`field-${index}`" label="Feld"></f-select>
            <f-select
                :id="`comparator-${index}`"
                :options="comparatorOptions"
                :model-value="condition.comparator"
                :name="`comparator-${index}`"
                label="Vergleich"
                @update:model-value="updateComparator(condition, $event)"
            ></f-select>
            <f-select
                v-if="condition.field && ['isEqual', 'isNotEqual'].includes(condition.comparator)"
                :id="`value-${index}`"
                v-model="condition.value"
                :options="getOptions(condition.field)"
                :name="`value-${index}`"
                label="Wert"
            ></f-select>
            <f-multipleselect
                v-if="condition.field && ['isIn', 'isNotIn'].includes(condition.comparator)"
                :id="`value-${index}`"
                v-model="condition.value"
                :options="getOptions(condition.field)"
                :name="`value-${index}`"
                label="Wert"
            ></f-multipleselect>
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
    {id: 'isEqual', name: 'ist gleich', defaultValue: null},
    {id: 'isNotEqual', name: 'ist ungleich', defaultValue: null},
    {id: 'isIn', name: 'ist in', defaultValue: []},
    {id: 'isNotIn', name: 'ist nicht in', defaultValue: []},
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

function updateComparator(condition, comparator) {
    condition.value = comparatorOptions.value.find((c) => c.id === comparator).defaultValue;
    condition.comparator = comparator;
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
