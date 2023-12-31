<template>
    <f-text id="rows" label="Zeilen" size="sm" name="rows" :model-value="modelValue.rows" type="number" min="1" @update:modelValue="$emit('update:modelValue', {...modelValue, rows: $event})"></f-text>
    <f-switch
        id="fieldrequired"
        v-model="modelValue.required"
        label="Erforderlich"
        size="sm"
        name="fieldrequired"
        inline
        @update:modelValue="$emit('update:modelValue', {...modelValue, required: $event})"
    ></f-switch>
</template>

<script setup>
const props = defineProps({
    modelValue: {},
    meta: {},
    payload: {},
});

const emit = defineEmits(['update:modelValue']);
</script>
