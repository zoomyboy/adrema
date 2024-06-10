<template>
    <div class="space-y-1">
        <span class="text-xs font-semibold text-gray-400">Optionen</span>
        <div v-for="(option, index) in modelValue.options" :key="index" class="flex space-x-2">
            <f-text
                :id="`options-${index}`"
                size="sm"
                class="grow"
                :name="`options-${index}`"
                :model-value="option"
                @update:modelValue="$emit('update:modelValue', {...props.modelValue, options: setOption(props.modelValue.options, index, $event)})"
            ></f-text>
            <ui-action-button
                tooltip="Löschen"
                icon="trash"
                class="btn-danger"
                @click="$emit('update:modelValue', {...modelValue, options: removeOption(modelValue.options, index)})"
            ></ui-action-button>
        </div>
        <ui-icon-button icon="plus" @click="$emit('update:modelValue', {...modelValue, options: addOption(modelValue.options)})">Option einfügen</ui-icon-button>
        <f-text
            id="min"
            type="number"
            size="sm"
            name="min"
            label="Minimale Anzahl Elemente"
            :model-value="modelValue.min"
            @update:model-value="$emit('update:modelValue', {...modelValue, min: $event})"
        ></f-text>
        <f-text
            id="max"
            type="number"
            size="sm"
            name="max"
            label="Maximale Anzahl Elemente"
            :model-value="modelValue.max"
            @update:model-value="$emit('update:modelValue', {...modelValue, max: $event})"
        ></f-text>
    </div>
</template>

<script setup>
import useElements from './useElements.js';
const {addOption, setOption, removeOption} = useElements();

const props = defineProps({
    modelValue: {},
    meta: {},
    payload: {},
});

const emit = defineEmits(['update:modelValue']);
</script>
