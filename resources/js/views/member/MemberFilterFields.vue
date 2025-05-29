<template>
    <f-switch v-show="hasModule('bill')" id="ausstand" name="ausstand" label="Nur Ausstände" size="sm" v-model="filter.ausstand"></f-switch>
    <f-select id="has_vk" name="has_vk" label="Verhaltenskodex unterschrieben" size="sm" :options="meta.boolean_filter" v-model="filter.has_vk"></f-select>
    <f-select id="has_svk" name="has_svk" label="SVK unterschrieben" size="sm" :options="meta.boolean_filter" v-model="filter.has_svk"></f-select>
    <f-multipleselect id="group_ids" :options="meta.groups" label="Gruppierungen" size="sm" v-model="filter.group_ids"></f-multipleselect>
    <f-select v-show="hasModule('bill')" id="billKinds" name="billKinds" :options="meta.billKinds" label="Rechnung" size="sm" v-model="filter.bill_kind"></f-select>

    <div class="mt-5">
        <f-checkboxes-label>nach Mitgliedschaften</f-checkboxes-label>
        <button class="btn btn-primary label mt-2" @click.prevent="filter.memberships = [...filter.memberships, {...meta.default_membership_filter}]">
            <ui-sprite class="w-3 h-3 xl:mr-2" src="plus"></ui-sprite>
            <span class="hidden xl:inline">Hinzufügen</span>
        </button>
        <template v-for="(filter, index) in filter.memberships" :key="index">
            <f-multipleselect :id="`group_ids-multiple-${index}`" class="mt-4" v-model="filter.group_ids" :options="meta.groups" label="Gruppierung" size="sm"></f-multipleselect>
            <f-multipleselect :id="`activity_ids-multiple-${index}`" v-model="filter.activity_ids" :options="meta.filterActivities" label="Tätigkeiten" size="sm"></f-multipleselect>
            <f-multipleselect :id="`subactivity_ids-multiple-${index}`" v-model="filter.subactivity_ids" :options="meta.filterSubactivities" label="Untertätigkeiten" size="sm"></f-multipleselect>
        </template>
    </div>
</template>

<script setup>
import {inject, ref, watch} from 'vue';

const axios = inject('axios');

const emit = defineEmits(['update:modelValue']);

const props = defineProps({
    modelValue: {
        type: Object,
        required: true,
    },
});

const metaResponse = await axios.post('/api/member/search', {});
const meta = ref(metaResponse.data.meta);

const filter = ref({...props.modelValue});

watch(
    filter,
    function (newValue) {
        emit('update:modelValue', newValue);
    },
    {deep: true}
);
</script>
