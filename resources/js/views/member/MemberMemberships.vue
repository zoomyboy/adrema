<template>
    <div class="sidebar flex flex-col group is-bright">
        <page-header title="Mitgliedschaften" @close="$emit('close')">
            <template #toolbar>
                <page-toolbar-button v-if="single === null" color="primary" icon="plus" @click.prevent="create">Neue Mitgliedschaft</page-toolbar-button>
                <page-toolbar-button v-if="single !== null" color="primary" icon="undo" @click.prevent="single = null">Zurück</page-toolbar-button>
            </template>
        </page-header>

        <form v-if="single" class="p-6 grid gap-4 justify-start" @submit.prevent="submit">
            <f-select id="group_id" v-model="single.group_id" name="group_id" :options="data.meta.groups" label="Gruppierung" required></f-select>
            <f-select id="activity_id" v-model="single.activity_id" name="activity_id" :options="data.meta.activities" label="Tätigkeit" required></f-select>
            <f-select
                v-if="single.activity_id"
                id="subactivity_id"
                v-model="single.subactivity_id"
                name="subactivity_id"
                :options="data.meta.subactivities[single.activity_id]"
                label="Untertätigkeit"
            ></f-select>
            <f-switch id="has_promise" :model-value="single.promised_at !== null" label="Hat Versprechen" @update:modelValue="single.promised_at = $event ? '2000-02-02' : null"></f-switch>
            <f-text v-show="single.promised_at !== null" id="promised_at" v-model="single.promised_at" type="date" label="Versprechensdatum" size="sm"></f-text>
            <button type="submit" class="btn btn-primary">Absenden</button>
        </form>

        <div v-else class="grow">
            <table class="custom-table custom-table-light custom-table-sm text-sm">
                <thead>
                    <th>Tätigkeit</th>
                    <th>Untertätigkeit</th>
                    <th>Datum</th>
                    <th>Aktiv</th>
                    <th></th>
                </thead>

                <tr v-for="(membership, index) in data.data" :key="index">
                    <td v-text="membership.activity_name"></td>
                    <td v-text="membership.subactivity_name"></td>
                    <td v-text="membership.human_date"></td>
                    <td><ui-boolean-display :value="membership.is_active" dark></ui-boolean-display></td>
                    <td class="flex space-x-1">
                        <a href="#" class="inline-flex btn btn-warning btn-sm" @click.prevent="edit(membership)"><ui-sprite src="pencil"></ui-sprite></a>
                        <a href="#" class="inline-flex btn btn-danger btn-sm" @click.prevent="remove(membership)"><ui-sprite src="trash"></ui-sprite></a>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</template>

<script setup>
import {ref, inject, onBeforeMount} from 'vue';
const axios = inject('axios');

const props = defineProps({
    value: {
        type: Object,
        required: true,
    },
});

const single = ref(null);
const data = ref({
    meta: {},
    data: [],
});

async function reload() {
    data.value = (await axios.post(props.value.links.membership_index)).data;
}

onBeforeMount(async () => {
    await reload();
});

function create() {
    single.value = JSON.parse(JSON.stringify(data.value.meta.default));
}

function edit(membership) {
    single.value = JSON.parse(JSON.stringify(membership));
}

async function submit() {
    single.value.id ? await axios.patch(single.value.links.update, single.value) : await axios.post(data.value.meta.links.store, single.value);
    await reload();
    single.value = null;
}

async function remove(membership) {
    await axios.delete(membership.links.destroy);
    await reload();
}
</script>
