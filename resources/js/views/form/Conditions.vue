<template>
    <ui-note v-if="locked" class="mt-2" type="danger">
        Dieses Formular wurde bereits bearbeitet.<br>
        Bitte speichere es erst ab und editiere dann die Bedingungen.
    </ui-note>

    <div v-else>
        <f-select :id="`mode-${id}`" :name="`mode-${id}`" :model-value="modelValue.mode" :options="modeOptions" label="Modus" @update:model-value="changeMode" />

        <ui-icon-button class="mt-4 mb-2" icon="plus" @click="addCondition">Bedingung einfügen</ui-icon-button>

        <div v-for="(condition, index) in modelValue.ifs" :key="index" class="grid grid-cols-[1fr_1fr_1fr_max-content] gap-2">
            <f-select :id="`field-${index}-${id}`"
                      :model-value="condition.field"
                      :options="fieldOptions"
                      :name="`field-${index}-${id}`"
                      label="Feld"
                      @update:model-value="update(index, 'field', $event)"
            />
            <f-select :id="`comparator-${index}-${id}`"
                      :options="comparatorOptions"
                      :model-value="condition.comparator"
                      :name="`comparator-${index}-${id}`"
                      label="Vergleich"
                      @update:model-value="update(index, 'comparator', $event)"
            />
            <f-select v-if="condition.field && ['isEqual', 'isNotEqual'].includes(condition.comparator) && ['RadioField', 'DropdownField', 'GroupField'].includes(getField(condition.field).type)"
                      :id="`value-${index}-${id}`"
                      v-model="condition.value"
                      :options="getOptions(condition.field)"
                      :name="`value-${index}-${id}`"
                      label="Wert"
            />
            <f-multipleselect v-if="condition.field && ['isIn', 'isNotIn'].includes(condition.comparator) && ['RadioField', 'DropdownField', 'GroupField'].includes(getField(condition.field).type)"
                              :id="`value-${index}-${id}`"
                              v-model="condition.value"
                              :options="getOptions(condition.field)"
                              label="Wert"
            />
            <f-switch v-if="condition.field && condition.comparator && ['CheckboxField'].includes(getField(condition.field).type)"
                      :id="`value-${index}-${id}`"
                      v-model="condition.value"
                      :name="`value-${index}-${id}`"
                      label="Wert"
            />
            <ui-action-button tooltip="Löschen" icon="trash" class="btn-danger self-end h-8" @click="remove(index)" />
        </div>
    </div>
</template>

<script setup>
import {ref, inject, computed, onMounted} from 'vue';
const axios = inject('axios');
const emit = defineEmits(['update:modelValue']);

const props = defineProps({
    modelValue: {
        required: true,
        type: Object,
    },
    single: {
        required: true,
        type: Object,
    },
    id: {
        required: true,
        type: String,
    },
});

const comparatorOptions = ref([
    {id: 'isEqual', name: 'ist gleich', defaultValue: {DropdownField: null, RadioField: null, CheckboxField: false, GroupField: null}},
    {id: 'isNotEqual', name: 'ist ungleich', defaultValue: {DropdownField: null, RadioField: null, CheckboxField: false, GroupField: null}},
    {id: 'isIn', name: 'ist in', defaultValue: {DropdownField: [], RadioField: [], CheckboxField: false, GroupField: []}},
    {id: 'isNotIn', name: 'ist nicht in', defaultValue: {DropdownField: [], RadioField: [], CheckboxField: false, GroupField: []}},
]);

const modeOptions = ref([
    {id: 'all', name: 'alle Bedingungen müssen zutreffen'},
    {id: 'any', name: 'mindestens eine Bedingung muss zutreffen'},
]);

const fields = computed(() => {
    const result = [];
    props.single.config.sections.forEach((section) => {
        section.fields.forEach((field) => {
            if (['DropdownField', 'RadioField', 'CheckboxField', 'GroupField'].includes(field.type)) {
                result.push(field);
            }
        });
    });

    return result;
});

function changeMode(mode) {
    emit('update:modelValue', {...props.modelValue, mode: mode});
}

function update(index, key, value) {
    const inner = {...props.modelValue};
    if (key === 'comparator') {
        var old = inner.ifs[index];
        inner.ifs[index] = {
            field: old.field,
            comparator: value,
            value: old.field ? comparatorOptions.value.find((c) => c.id === value).defaultValue[getField(old.field).type] : null,
        };
    }
    if (key === 'field') {
        var old = inner.ifs[index];
        inner.ifs[index] = {
            field: value,
            comparator: null,
            value: null,
        };
    }
    emit('update:modelValue', inner);
}

function remove(index) {
    emit('update:modelValue', {...props.modelValue, ifs: props.modelValue.ifs.toSpliced(index, 1)});
}

function getField(fieldName) {
    return fields.value.find((f) => f.key === fieldName);
}

function getOptions(fieldName) {
    if (getField(fieldName).type === 'GroupField') {
        return groupOptions.value;
    }

    return getField(fieldName).options.map((o) => {
        return {id: o, name: o};
    });
}

const fieldOptions = computed(() =>
    fields.value.map((field) => {
        return {id: field.key, name: field.name};
    })
);

function addCondition() {
    emit('update:modelValue', {
        ...props.modelValue,
        ifs: [
            ...props.modelValue.ifs,
            {
                field: null,
                comparator: null,
                value: null,
            },
        ],
    });
}

const locked = ref(false);

async function checkIfDirty() {
    const response = await axios.post(props.single.links.is_dirty, {config: props.single.config});

    locked.value = response.data.result;
}

if (props.single.links && props.single.links.is_dirty) {
    checkIfDirty();
}

const groupOptions = ref([]);

onMounted(async () => {
    groupOptions.value = (await axios.get('/api/group?all')).data.data;
});
</script>
