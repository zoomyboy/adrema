<template>
    <div class="flex rounded-lg overflow-hidden">
        <div v-for="(prevention, index) in value" :key="index" v-tooltip="prevention.tooltip" class="py-1 px-2 text-xs" :class="classes(prevention)" v-text="prevention.letter"></div>
    </div>
</template>

<script setup>
const props = defineProps({
    value: {
        required: true,
        type: Object,
    },
});

function classes(prevention) {
    return prevention.value ? 'bg-green-800 text-green-200' : 'bg-neutral-700 text-neutral-200';
}
</script>
