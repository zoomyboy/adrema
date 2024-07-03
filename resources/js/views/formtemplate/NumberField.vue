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
    <f-text id="min" :model-value="modelValue.min" label="minimaler Wert" size="sm" type="number" @update:modelValue="$emit('update:modelValue', {...modelValue, min: parse($event)})"></f-text>
    <f-text id="max" :model-value="modelValue.max" label="maximaler Wert" size="sm" type="number" @update:modelValue="$emit('update:modelValue', {...modelValue, max: parse($event)})"></f-text>
</template>

<script lang="js" setup>
const props = defineProps({
    modelValue: {},
    meta: {},
    payload: {},
});

function parse(input) {
    return parseInt(input) === NaN ? null : parseInt(input);
}

const emit = defineEmits(['update:modelValue']);
</script>
