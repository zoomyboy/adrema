<template>
    <div>
        <ui-popup v-for="(popup, index) in swal.popups" :key="index" :heading="popup.title">
            <div class="space-y-4 mt-4">
                <div v-text="popup.body" />
                <div class="space-x-4">
                    <ui-button type="button" class="btn-danger" @click.prevent="popup.resolve(popup.id)">{{ popup.confirmButton }}</ui-button>
                    <ui-button type="button" class="btn-success" @click.prevent="popup.reject(popup.id)">{{ popup.cancelButton }}</ui-button>
                </div>
            </div>
        </ui-popup>
    </div>
</template>

<script lang="ts" setup>
import useSwal from '@/stores/swalStore.ts';
const swal = useSwal();
</script>
