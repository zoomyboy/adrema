<template>
    <th @click="$emit('update:model-value')">
        <div class="flex justify-between items-center">
            <span v-text="label"></span>
            <ui-sprite v-if="value.by === column && value.direction === false" src="chevron" class="rotate-180 w-3 h-3 text-primaryfg ml-2">ASC</ui-sprite>
            <ui-sprite v-if="value.by === column && value.direction === true" src="chevron" class="w-3 h-3 text-primaryfg ml-2">DESC</ui-sprite>
        </div>
    </th>
</template>

<script setup>
defineEmits(['update:model-value']);

defineProps({
    column: {
        type: String,
        default: () => '',
    },
    label: {
        type: String,
        default: () => '',
    },
    value: {
        type: Object,
        default: () => {
            return {by: '', direction: false};
        },
    },
    sortable: {
        type: Boolean,
        default: false,
    },
});
</script>
