<template>
    <div class="space-y-1">
        <span class="text-xs font-semibold text-gray-400">Optionen</span>
        <div v-for="(option, index) in modelValue.options" :key="index" class="flex space-x-2">
            <f-text
                :id="`options-${index}`"
                size="sm"
                class="grow"
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
    </div>
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
        id="allowcustom"
        :model-value="modelValue.allowcustom"
        label="Eigene Option erlauben"
        size="sm"
        name="allowcustom"
        inline
        @update:modelValue="$emit('update:modelValue', {...modelValue, allowcustom: $event})"
    ></f-switch>
</template>

<script lang="js" setup>
import useElements from './useElements.js';
const { addOption, setOption, removeOption } = useElements();

const props = defineProps({
    modelValue: {},
    meta: {},
    payload: {},
});

const emit = defineEmits(['update:modelValue']);
</script>
