<template>
    <span class="font-semibold leading-none text-gray-400 group-[.field-base]:text-sm group-[.field-sm]:text-xs">
        {{ value }}
        <span v-show="required" class="text-red-800">&nbsp;*</span>
    </span>
</template>

<script setup>
const props = defineProps({
    value: {
        type: String,
        required: true,
    },
    required: {
        type: Boolean,
        required: true,
    },
});
</script>
