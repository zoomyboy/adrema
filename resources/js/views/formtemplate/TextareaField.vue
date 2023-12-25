<template>
    <div>
        <f-text
            id="rows"
            label="Zeilen"
            size="sm"
            name="rows"
            :model-value="modelValue.rows"
            type="number"
            min="1"
            @update:modelValue="$emit('update:modelValue', {...modelValue, rows: $event})"
        ></f-text>
    </div>
</template>

<script setup>
const props = defineProps({
    modelValue: {},
});

const emit = defineEmits(['update:modelValue']);
</script>
