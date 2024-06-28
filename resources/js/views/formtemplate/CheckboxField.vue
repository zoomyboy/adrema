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
    <f-textarea
        id="description"
        label="Beschreibung"
        size="sm"
        :model-value="modelValue.description"
        @update:modelValue="$emit('update:modelValue', {...modelValue, description: $event})"
    ></f-textarea>
</template>

<script setup>
const props = defineProps({
    modelValue: {},
    meta: {},
    payload: {},
});

const emit = defineEmits(['update:modelValue']);
</script>
