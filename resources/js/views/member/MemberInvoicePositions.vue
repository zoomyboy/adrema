<template>
    <page-header title="Zahlungen" @close="$emit('close')"> </page-header>

    <div class="grow">
        <table class="custom-table custom-table-light custom-table-sm text-sm">
            <thead>
                <th>Beschreibung</th>
                <th>Status</th>
                <th>Beitrag</th>
            </thead>

            <tr v-for="(position, index) in data" :key="index">
                <td v-text="position.description"></td>
                <td v-text="position.invoice.status"></td>
                <td v-text="position.price_human"></td>
            </tr>
        </table>
    </div>
</template>

<script setup>
defineEmits(['close']);
import { useApiIndex } from '../../composables/useApiIndex.js';

const props = defineProps({
    url: {
        type: String,
        required: true,
    },
});

const { data, reload } = useApiIndex(props.url, 'payment');

await reload();
</script>
