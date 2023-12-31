<template>
    <div class="space-y-1">
        <span class="text-xs font-semibold text-gray-400">Optionen</span>
        <f-text
            v-for="(option, index) in modelValue.options"
            :id="`options-${index}`"
            :key="index"
            size="sm"
            :name="`options-${index}`"
            :model-value="option"
            @update:modelValue="$emit('update:modelValue', {...props.modelValue, options: props.modelValue.options.toSpliced(index, 1, $event)})"
        ></f-text>
        <ui-icon-button icon="plus" @click="$emit('update:modelValue', {...modelValue, options: [...modelValue.options, '']})">Option einfügen</ui-icon-button>
    </div>
</template>

<script setup>
const props = defineProps({
    modelValue: {},
    meta: {},
    payload: {},
});

const emit = defineEmits(['update:modelValue']);
</script>
