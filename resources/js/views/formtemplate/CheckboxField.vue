<template>
    <div>
        <f-textarea
            id="description"
            label="Beschreibung"
            size="sm"
            name="description"
            :model-value="modelValue.description"
            @update:modelValue="$emit('update:modelValue', {...modelValue, description: $event})"
        ></f-textarea>
    </div>
</template>

<script setup>
const props = defineProps({
    modelValue: {},
});

const emit = defineEmits(['update:modelValue']);
</script>
