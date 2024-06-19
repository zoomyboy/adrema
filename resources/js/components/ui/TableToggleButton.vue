<template>
    <div class="flex space-x-2" :class="levelMap[level]">
        <a
            v-if="childrenCount > 0"
            v-tooltip="active ? 'Schließen' : 'Öffnen'"
            href="#"
            class="inline-flex items-center justify-center bg-blue-700 text-blue-100 rounded w-5 h-5"
            @click.prevent="emit('toggle')"
        >
            <ui-sprite class="w-3 h-3" :src="active ? 'close' : 'plus'"></ui-sprite>
        </a>
        <span v-text="text"></span>
    </div>
</template>

<script setup>
const emit = defineEmits(['toggle']);

const levelMap = {
    0: '',
    1: 'pl-7',
    2: 'pl-14',
};

const props = defineProps({
    text: {
        required: true,
        type: String,
    },
    childrenCount: {
        type: Number,
        required: true,
    },
    active: {
        type: Boolean,
        required: true,
    },
    level: {
        required: true,
        type: Number,
    },
});
</script>
