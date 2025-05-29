<template>
    <page-filter-sidebar v-model="visible">
        <slot name="fields"></slot>
    </page-filter-sidebar>
    <div class="px-6 py-2 border-b border-gray-600 items-center space-x-3">
        <div class="flex flex-col sm:flex-row items-stretch sm:items-end space-y-1 sm:space-y-0 sm:space-x-3">
            <slot name="buttons"></slot>
            <ui-icon-button v-if="!!$slots.fields" icon="filter" @click="visible = true">Filtern</ui-icon-button>
        </div>
    </div>
</template>

<script setup>
import {ref} from 'vue';

const visible = ref(false);
</script>
