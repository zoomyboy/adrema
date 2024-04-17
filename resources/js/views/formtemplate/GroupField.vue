<template>
    <f-switch
        id="fieldrequired"
        v-model="modelValue.required"
        label="Erforderlich"
        size="sm"
        name="fieldrequired"
        inline
        @update:modelValue="$emit('update:modelValue', {...modelValue, required: $event})"
    ></f-switch>
    <f-switch
        id="has_empty_option"
        v-model="modelValue.has_empty_option"
        label="Leere Option erlauben"
        size="sm"
        name="has_empty_option"
        inline
        @update:modelValue="$emit('update:modelValue', {...modelValue, has_empty_option: $event})"
    ></f-switch>
    <f-text
        v-if="modelValue.has_empty_option"
        id="empty_option_value"
        v-model="modelValue.empty_option_value"
        label="Wert der leeren Option"
        size="sm"
        name="empty_option_value"
        @update:modelValue="$emit('update:modelValue', {...modelValue, empty_option_value: $event})"
    ></f-text>
    <f-select
        id="parent_field"
        :options="fieldOptions"
        size="sm"
        name="parent_field"
        label="Übergeordnetes Feld"
        :model-value="modelValue.parent_field"
        @update:modelValue="$emit('update:modelValue', {...props.modelValue, parent_field: $event})"
    ></f-select>
    <f-select
        id="parent_group"
        :options="meta.groups"
        size="sm"
        name="parent_group"
        label="Übergeordnete Gruppe"
        :model-value="modelValue.parent_group"
        @update:modelValue="$emit('update:modelValue', {...props.modelValue, parent_group: $event})"
    ></f-select>
</template>

<script setup>
import {computed} from 'vue';

const props = defineProps({
    modelValue: {},
    meta: {},
    payload: {},
});

const fieldOptions = computed(() => {
    return props.payload.reduce((carry, section) => {
        return section.fields.reduce((fcarry, field) => {
            return field.type === 'GroupField' ? fcarry.concat({id: field.key, name: field.name}) : fcarry;
        }, carry);
    }, []);
});

const emit = defineEmits(['update:modelValue']);
</script>
