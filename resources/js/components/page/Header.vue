<template>
    <div class="h-16 px-6 flex items-center justify-between border-b border-solid border-gray-600 group-[.is-bright]:border-gray-500">
        <div class="flex items-center space-x-2">
            <slot name="before-title"></slot>
            <page-title>{{ title }}</page-title>
            <slot name="toolbar"></slot>
        </div>
        <div class="flex items-center space-x-4 ml-2">
            <a v-if="$attrs.onClose" href="#" class="btn label btn-primary-light icon" @click.prevent="$emit('close')">
                <ui-sprite class="w-3 h-3" src="close"></ui-sprite>
            </a>
            <slot name="right"></slot>
        </div>
    </div>
</template>

<script setup>
defineProps({
    title: {
        type: String,
        default: function () {
            return '';
        },
    },
});
</script>
