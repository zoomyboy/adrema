<template>
    <page-layout>
        <template #right>
            <f-save-button form="actionform"></f-save-button>
        </template>
        <form id="actionform" class="grow p-3" @submit.prevent="submit">
            <div class="flex space-x-3">
                <f-select
                    :model-value="meta.activity_id"
                    :options="props.activities"
                    label="Tätigkeit"
                    size="sm"
                    name="activity_id"
                    @update:model-value="meta = {...meta, activity_id: $event, subactivity_id: null}"
                ></f-select>
                <f-select v-model="meta.subactivity_id" :options="props.subactivities[meta.activity_id]" name="subactivity_id" label="Untertätigkeit" size="sm"></f-select>
                <f-select v-model="meta.group_id" :options="props.groups" label="Gruppierung" size="sm" name="group_id"></f-select>
                <f-text id="search_text" v-model="searchText" label="Suchen …" size="sm" name="search_text"></f-text>
            </div>
            <div class="grid gap-2 grid-cols-6 mt-4">
                <f-switch v-for="member in members.hits" :id="`member-${member.id}`" :key="member.id" v-model="selected" :value="member.id" :label="member.fullname" size="sm"></f-switch>
            </div>
            <div class="px-6">
                <ui-search-pagination class="mt-4" :value="members" @reload="reloadReal($event)"></ui-search-pagination>
            </div>
        </form>
    </page-layout>
</template>

<script lang="js" setup>
import { onBeforeUnmount, ref, defineProps, watch, inject } from 'vue';
import useQueueEvents from '../../composables/useQueueEvents.js';
import useSearch from '../../composables/useSearch.js';

const { startListener, stopListener } = useQueueEvents('group', () => null);
const { search } = useSearch();
const axios = inject('axios');
startListener();
onBeforeUnmount(() => stopListener());

const props = defineProps({
    activities: {
        type: Object,
        default: () => { },
    },
    subactivities: {
        type: Object,
        default: () => { },
    },
    groups: {
        type: Object,
        default: () => { },
    },
});

const searchText = ref('');
watch(searchText, (newValue) => {
    reloadReal(1);
});

const meta = ref({
    activity_id: null,
    subactivity_id: null,
    group_id: null,
});

const members = ref({
    hits: [],
    hitsPerPage: 1,
    page: 1,
    totalHits: 0,
    totalPages: 1,
});

const selected = ref([]);

watch(meta, async (newValue) => {
    if (!newValue.group_id || !newValue.subactivity_id || !newValue.activity_id) {
        return;
    }
    const results = await search('', [`memberships.with_group = "${newValue.group_id}|${newValue.activity_id}|${newValue.subactivity_id}"`], { page: 1, hitsPerPage: 1000000 });
    selected.value = results.hits.map(member => member.id);
}, { deep: true });

async function reloadReal(page) {
    const results = await search(searchText.value, [], { page: page, hitsPerPage: 80 });
    members.value = results;
}
reloadReal(1);

async function submit() {
    await axios.post('/api/membership/masslist', {
        ...meta.value,
        members: selected.value,
    });
}
</script>
