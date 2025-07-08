<template>
    <ui-popup v-for="(popup, index) in swal.popups" :key="index" :icon="popup.icon" :heading="popup.title" @close="popup.reject(popup.id)">
        <div class="text-center mt-4" v-text="popup.body" />
        <div class="mt-4">
            <template v-for="field in popup.fields">
                <f-text v-if="field.type === 'text'" :id="field.name" :key="field.name" v-model="popup.payload[field.name]" :name="field.name" :label="field.label" />
                <f-select v-if="field.type === 'select'" :id="field.name" :key="field.name" v-model="popup.payload[field.name]" :name="field.name" :label="field.label" :options="field.options" />
            </template>
        </div>
        <div class="flex justify-center space-x-4 mt-6">
            <ui-button type="button" class="btn-primary" @click.prevent="popup.resolve(popup.id)">{{ popup.confirmButton }}</ui-button>
            <ui-button type="button" class="btn-default" @click.prevent="popup.reject(popup.id)">{{ popup.cancelButton }}</ui-button>
        </div>
    </ui-popup>
</template>

<script lang="ts" setup>
import useSwal from '@/stores/swalStore.ts';
const swal = useSwal();
</script>

