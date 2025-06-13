<template>
    <page-layout>
        <page-filter>
            <template #fields>
                <f-multipleselect id="groups"
                                  :model-value="getFilter('groups')"
                                  :options="meta.groups"
                                  label="Gruppen"
                                  name="groups"
                                  @update:model-value="setFilter('groups', $event)"
                />
                <f-multipleselect id="activities"
                                  :model-value="getFilter('activities')"
                                  :options="meta.activities"
                                  label="Tätigkeiten"
                                  name="activities"
                                  @update:model-value="setFilter('activities', $event)"
                />
                <f-multipleselect id="subactivities"
                                  :model-value="getFilter('subactivities')"
                                  :options="meta.subactivities"
                                  label="Untertätigkeiten"
                                  name="subactivities"
                                  @update:model-value="setFilter('subactivities', $event)"
                />
                <f-select id="active"
                          :options="[{id: true, name: 'nur aktive'}, {id: false, name: 'nur inaktive'}]"
                          :model-value="getFilter('active')"
                          label="Aktiv"
                          name="active"
                          @update:model-value="setFilter('active', $event)"
                />
            </template>
        </page-filter>
        <div class="flex space-x-3" />
        <table cellspacing="0" cellpadding="0" border="0" class="custom-table custom-table-sm table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Gruppierung</th>
                    <th>Tätigkeit</th>
                    <th>Untertätigkeit</th>
                    <th>Beginn</th>
                    <th>Ende</th>
                    <th>Aktiv</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(membership, index) in data" :key="index">
                    <td v-text="membership.member.fullname" />
                    <td v-text="membership.group.name" />
                    <td v-text="membership.activity.name" />
                    <td v-text="membership.subactivity?.name" />
                    <td v-text="membership.from.human" />
                    <td v-text="membership.to?.human" />
                    <td><ui-bool :value="membership.isActive" /></td>
                </tr>
            </tbody>
        </table>

        <div class="px-6">
            <ui-pagination class="mt-4" :value="meta" :only="['data', 'meta']" />
        </div>
    </page-layout>
</template>

<script lang="js" setup>
import {useIndex, indexProps} from '@/composables/useIndex.js';

const props = defineProps(indexProps);

const {data, meta, getFilter, setFilter} = useIndex(props.data, 'memberships');

</script>
