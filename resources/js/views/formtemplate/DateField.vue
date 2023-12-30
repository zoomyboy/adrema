<template>
    <f-switch
        id="fieldrequired"
        :model-value="modelValue.required"
        label="Erforderlich"
        size="sm"
        name="fieldrequired"
        inline
        @update:modelValue="$emit('update:modelValue', {...modelValue, required: $event})"
    ></f-switch>
    <f-switch
        id="max_today"
        :model-value="modelValue.max_today"
        label="Nur Daten bis heute erlauben"
        size="sm"
        name="max_today"
        inline
        @update:modelValue="$emit('update:modelValue', {...modelValue, max_today: $event})"
    ></f-switch>
</template>

<script setup>
const props = defineProps({
    modelValue: {},
});

const emit = defineEmits(['update:modelValue']);
</script>
