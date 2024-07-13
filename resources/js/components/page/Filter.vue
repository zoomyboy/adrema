<template>
    <ui-popup v-if="visible === true" heading="Filtern" @close="visible = false">
        <div class="grid gap-3 md:grid-cols-2">
            <slot name="fields"></slot>
        </div>
    </ui-popup>
    <div class="px-6 py-2 border-b border-gray-600" :class="visibleDesktopBlock">
        <div class="flex items-end space-x-3">
            <slot name="buttons"></slot>
            <ui-icon-button v-if="filterable" icon="filter" @click="filterVisible = !filterVisible">Filtern</ui-icon-button>
        </div>
        <ui-box v-if="filterVisible" class="mt-3">
            <div class="grid grid-cols-4 gap-3 items-end">
                <slot name="fields"></slot>
                <ui-icon-button class="col-start-1" icon="close" @click="filterVisible = false">Schließen</ui-icon-button>
            </div>
        </ui-box>
    </div>
    <div class="px-6 py-2 border-b border-gray-600 items-center space-x-3" :class="visibleMobile">
        <div class="flex flex-col sm:flex-row items-stretch sm:items-end space-y-1 sm:space-y-0 sm:space-x-3">
            <slot name="buttons"></slot>
            <ui-icon-button v-if="filterable" icon="filter" @click="visible = true">Filtern</ui-icon-button>
        </div>
    </div>
</template>

<script setup>
import {defineProps, ref} from 'vue';
import useBreakpoints from '../../composables/useBreakpoints.js';

const visible = ref(false);

const filterVisible = ref(false);

const props = defineProps({
    breakpoint: {
        type: String,
        required: true,
    },
    filterable: {
        type: Boolean,
        default: () => true,
    },
});

const {visibleDesktopBlock, visibleMobile} = useBreakpoints(props);
</script>
