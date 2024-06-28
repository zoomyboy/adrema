<template>
    <div class="h-full items-center flex absolute top-0 right-0">
        <div v-tooltip="value">
            <ui-sprite src="info-button" class="w-5 h-5 text-primary-700 mr-2"></ui-sprite>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    value: {
        type: String,
        required: true,
    },
});
</script>
