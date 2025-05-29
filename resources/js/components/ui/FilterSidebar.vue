<template>
    <ui-sidebar :max="0" v-if="modelValue === true" @close="$emit('update:modelValue', false)">
        <page-header title="Filter" @close="$emit('update:modelValue', false)"> </page-header>
        <div class="grid gap-3 p-6">
            <slot></slot>
        </div>
    </ui-sidebar>
</template>

<script setup>
defineEmits(['update:modelValue']);

defineProps({
    modelValue: {
        type: Boolean,
        required: true,
    },
});
</script>
