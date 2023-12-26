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
</template>

<script setup>
const props = defineProps({
    modelValue: {
        validator: (v) => v === true || v === false,
    },
});

const emit = defineEmits(['update:modelValue']);
</script>
