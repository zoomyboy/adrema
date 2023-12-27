<template>
    <f-switch id="fieldrequired" :model-value="modelValue.required" label="Erforderlich" size="sm" name="fieldrequired"
        inline @update:modelValue="$emit('update:modelValue', { ...modelValue, required: $event })"></f-switch>
</template>

<script setup>
const props = defineProps({
    modelValue: {},
});

const emit = defineEmits(['update:modelValue']);
</script>
