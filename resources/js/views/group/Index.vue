<template>
    <page-layout>
        <template #right>
            <f-save-button form="actionform"></f-save-button>
        </template>
        <form id="actionform" class="grow p-3" @submit.prevent="submit">
            <div class="flex space-x-3">
                <f-select :model-value="meta.activity_id" :options="props.activities" label="Tätigkeit" size="sm" name="activity_id" @update:model-value="setActivityId"></f-select>
                <f-select
                    :model-value="meta.subactivity_id"
                    :options="props.subactivities[meta.activity_id]"
                    name="subactivity_id"
                    label="Untertätigkeit"
                    size="sm"
                    @update:model-value="reload('subactivity_id', $event)"
                ></f-select>
                <f-select :model-value="meta.group_id" :options="props.groups" label="Gruppierung" size="sm" name="group_id" @update:model-value="reload('group_id', $event)"></f-select>
            </div>
            <div class="grid gap-2 grid-cols-6 mt-4">
                <f-switch v-for="member in members.data" :id="`member-${member.id}`" :key="member.id" v-model="selected" :value="member.id" :label="member.fullname" size="sm"></f-switch>
            </div>
            <div v-if="members.meta.last_page" class="px-6">
                <ui-pagination class="mt-4" :value="members.meta" :only="['data']" @reload="reloadReal($event, false)"></ui-pagination>
            </div>
        </form>
    </page-layout>
</template>

<script lang="js" setup>
import { onBeforeUnmount, ref, defineProps, reactive, inject } from 'vue';
import useQueueEvents from '../../composables/useQueueEvents.js';
const {startListener, stopListener} = useQueueEvents('group', () => null);
const axios = inject('axios');

startListener();
onBeforeUnmount(() => stopListener());

const meta = reactive({
    activity_id: null,
    subactivity_id: null,
    group_id: null,
});

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

const members = ref({ meta: {}, data: [] });
const selected = ref([]);

async function reload(key, v) {
    meta[key] = v;

    reloadReal(1, true);
}

async function reloadReal(page, update) {
    if (meta.activity_id && meta.subactivity_id && meta.group_id) {
        const memberResponse = await axios.post('/api/member/search', {
            page: page,
            per_page: 80,
        });
        members.value = memberResponse.data;

        if (update) {
            const membershipResponse = await axios.post('/api/membership/member-list', meta);
            selected.value = membershipResponse.data;
        }
    }
}

async function setActivityId(id) {
    meta.subactivity_id = null;
    await reload('activity_id', id);
}

async function submit() {
    await axios.post('/api/membership/sync', {
        ...meta,
        members: selected.value,
    });
}
</script>
