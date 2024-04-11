<template>
    <a v-tooltip="tooltip" :href="href" :target="blank ? '_BLANK' : '_SELF'" class="inline-flex btn btn-sm">
        <ui-sprite :src="icon"></ui-sprite>
    </a>
</template>

<script setup>
defineProps({
    tooltip: {
        required: true,
        type: String,
    },
    href: {
        type: String,
        default: () => '#',
        required: false,
    },
    blank: {
        type: Boolean,
        default: () => false,
        required: false,
    },
    icon: {
        type: String,
        required: true,
    },
});
</script>
