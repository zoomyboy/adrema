<template>
    <ui-popup v-if="visible === true" heading="Filtern" @close="visible = false">
        <div class="grid gap-3 md:grid-cols-2">
            <slot></slot>
        </div>
    </ui-popup>
    <div class="px-6 py-2 border-b border-gray-600 items-center space-x-3" :class="visibleDesktop">
        <slot></slot>
    </div>
    <div class="px-6 py-2 border-b border-gray-600 items-center space-x-3" :class="visibleMobile">
        <ui-icon-button icon="filter" @click="visible = true">Filtern</ui-icon-button>
    </div>
</template>

<script setup>
import {defineProps, ref} from 'vue';
import useBreakpoints from '../../composables/useBreakpoints.js';

const visible = ref(false);

const props = defineProps({
    breakpoint: {
        type: String,
        required: true,
    },
});

const {visibleDesktop, visibleMobile} = useBreakpoints(props);
</script>
