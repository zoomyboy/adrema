<template>
    <page-layout page-class="pb-6">
        <template #toolbar>
            <page-toolbar-button :href="meta.links.create" color="primary" icon="plus">Tätigkeit erstellen</page-toolbar-button>
        </template>
        <ui-popup v-if="deleting !== null" heading="Bitte bestätigen" @close="deleting = null">
            <div>
                <p class="mt-4">Diese Aktivität löschen?</p>
                <div class="grid grid-cols-2 gap-3 mt-6">
                    <a href="#" class="text-center btn btn-danger" @click.prevent="remove">Löschen</a>
                    <a href="#" class="text-center btn btn-primary" @click.prevent="deleting = null">Abbrechen</a>
                </div>
            </div>
        </ui-popup>
        <table cellspacing="0" cellpadding="0" border="0" class="custom-table custom-table-sm table">
            <thead>
                <th>Name</th>
                <th></th>
            </thead>

            <tr v-for="(activity, index) in data" :key="index">
                <td v-text="activity.name"></td>
                <td>
                    <div class="flex space-x-1">
                        <i-link v-tooltip="`bearbeiten`" :href="activity.links.edit" class="inline-flex btn btn-warning btn-sm"><ui-sprite src="pencil"></ui-sprite></i-link>
                        <a v-tooltip="`Entfernen`" href="#" class="inline-flex btn btn-danger btn-sm" @click.prevent="deleting = activity"><ui-sprite src="trash"></ui-sprite></a>
                    </div>
                </td>
            </tr>
        </table>

        <div class="px-6">
            <ui-pagination class="mt-4" :value="meta" :only="['data']"></ui-pagination>
        </div>
    </page-layout>
</template>

<script setup>
import {ref, defineProps} from 'vue';
import {indexProps, useIndex} from '../../composables/useIndex.js';

const props = defineProps(indexProps);
const {router, data, meta} = useIndex(props.data);
const deleting = ref(null);

function remove() {
    router.delete(deleting.value.links.destroy, {
        preserveState: true,
        onSuccess: (page) => {
            data.value = page.props.data.data;
            meta.value = page.props.data.meta;
            deleting.value = null;
        },
    });
}
</script>
