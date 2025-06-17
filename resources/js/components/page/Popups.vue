<template>
    <ui-popup v-for="(popup, index) in swal.popups" :key="index" :icon="popup.icon" :heading="popup.title" @close="popup.reject(popup.id)">
        <div class="mt-4">
            <div class="text-center" v-text="popup.body" />
            <div class="flex justify-center space-x-4 mt-8">
                <ui-button type="button" class="btn-primary" @click.prevent="popup.resolve(popup.id)">{{ popup.confirmButton }}</ui-button>
                <ui-button type="button" class="btn-default" @click.prevent="popup.reject(popup.id)">{{ popup.cancelButton }}</ui-button>
            </div>
        </div>
    </ui-popup>
</template>

<script lang="ts" setup>
import useSwal from '@/stores/swalStore.ts';
const swal = useSwal();
</script>

