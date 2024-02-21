<template>
    <div class="mt-5">
        <table cellspacing="0" cellpadding="0" border="0" class="custom-table custom-table-sm">
            <thead>
                <th v-for="column in activeColumns" :key="column.id" v-text="column.name"></th>
                <th></th>
            </thead>

            <tr v-for="(form, index) in data" :key="index">
                <td v-for="column in activeColumns" :key="column.id">
                    <div v-text="form[column.display_attribute]"></div>
                </td>
                <td>
                    <a v-tooltip="`Bearbeiten`" href="#" class="ml-2 inline-flex btn btn-warning btn-sm" @click.prevent="edit(form)"><ui-sprite src="pencil"></ui-sprite></a>
                    <a v-tooltip="`Löschen`" href="#" class="ml-2 inline-flex btn btn-danger btn-sm" @click.prevent="deleting = form"><ui-sprite src="trash"></ui-sprite></a>
                </td>
            </tr>
        </table>
        <div class="px-6">
            <ui-pagination class="mt-4" :value="meta" @reload="reloadPage"></ui-pagination>
        </div>
    </div>
</template>

<script setup>
import {ref, computed} from 'vue';
import {useApiIndex} from '../../composables/useApiIndex.js';
import FormBuilder from '../formtemplate/FormBuilder.vue';

const props = defineProps({
    url: {
        type: String,
        required: true,
        validator: (value) => value.startsWith('http'),
    },
});

var {meta, data, reload, reloadPage} = useApiIndex(props.url, 'participant');

await reload();

const activeColumns = computed(() => meta.value.columns.filter((c) => meta.value.form_meta.active_columns.includes(c.id)));
</script>
