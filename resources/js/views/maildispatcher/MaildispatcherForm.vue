<template>
    <page-layout>
        <template #toolbar>
            <page-toolbar-button :href="meta.links.index" color="primary" icon="undo">Zurück</page-toolbar-button>
        </template>
        <template #right>
            <f-save-button form="form"></f-save-button>
        </template>
        <form id="form" class="p-3 grid gap-3" @submit.prevent="submit">
            <ui-box heading="Metadatem">
                <div class="grid gap-4 sm:grid-cols-2">
                    <f-text id="name" v-model="model.name" label="Name" size="sm" required></f-text>
                    <f-select id="gateway_id" v-model="model.gateway_id" name="gateway_id" :options="meta.gateways" label="Verbindung" size="sm" required></f-select>
                </div>
            </ui-box>
            <ui-box v-if="members !== null" heading="Filterregeln">
                <div class="grid gap-4 sm:grid-cols-2">
                    <f-multipleselect
                        id="activity_ids"
                        v-model="model.filter.activity_ids"
                        :options="members.meta.filterActivities"
                        label="Tätigkeit"
                        size="sm"
                        @update:model-value="reload(1)"
                    ></f-multipleselect>
                    <f-multipleselect
                        id="subactivity_ids"
                        v-model="model.filter.subactivity_ids"
                        :options="members.meta.filterSubactivities"
                        label="Unterttätigkeit"
                        size="sm"
                        @update:model-value="reload(1)"
                    ></f-multipleselect>
                    <f-multipleselect
                        id="include"
                        v-model="model.filter.include"
                        :options="members.meta.members"
                        label="Zusätzliche Mitglieder"
                        size="sm"
                        @update:model-value="reload(1)"
                    ></f-multipleselect>
                    <f-multipleselect
                        id="exclude"
                        v-model="model.filter.exclude"
                        :options="members.meta.members"
                        label="Mitglieder ausschließen"
                        size="sm"
                        @update:model-value="reload(1)"
                    ></f-multipleselect>
                    <f-multipleselect id="groupIds" v-model="model.filter.group_ids" :options="members.meta.groups" label="Gruppierungen" size="sm" @update:model-value="reload(1)"></f-multipleselect>
                </div>
            </ui-box>
            <ui-box v-if="members !== null" heading="Mitglieder">
                <table cellspacing="0" cellpadding="0" border="0" class="custom-table custom-table-sm hidden md:table">
                    <thead>
                        <th></th>
                        <th>Nachname</th>
                        <th>Vorname</th>
                        <th>E-Mail-Adresse</th>
                        <th>E-Mail-Adresse Eltern</th>
                    </thead>

                    <tr v-for="(member, index) in members.data" :key="index">
                        <td><ui-age-groups :member="member"></ui-age-groups></td>
                        <td v-text="member.lastname"></td>
                        <td v-text="member.firstname"></td>
                        <td v-text="member.email"></td>
                        <td v-text="member.email_parents"></td>
                    </tr>
                </table>
                <ui-pagination class="mt-4" :value="members.meta" :only="['data']" @reload="reload"></ui-pagination>
            </ui-box>
        </form>
    </page-layout>
</template>

<script setup>
import {ref, inject, defineProps} from 'vue';
import {useIndex} from '../../composables/useIndex.js';

const props = defineProps({
    data: {
        default: () => undefined,
        type: Object,
    },
    meta: {
        type: Object,
        default: () => {},
    },
});

const {router} = useIndex({data: [], meta: {}}, 'maildispatcher');

const model = ref(props.data === undefined ? {...props.meta.default_model} : {...props.data});
const members = ref(null);
const axios = inject('axios');

async function reload(page) {
    members.value = (
        await axios.post('/api/member/search', {
            page: page,
            filter: model.value.filter,
        })
    ).data;
}

reload();

async function submit() {
    model.value.id ? await axios.patch(model.value.links.update, model.value) : await axios.post('/maildispatcher', model.value);
    router.visit(props.meta.links.index);
}
</script>
