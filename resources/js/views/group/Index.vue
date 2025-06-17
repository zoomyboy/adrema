<template>
    <page-layout>
        <template #right>
            <f-save-button form="groupform" />
        </template>
        <ui-popup v-if="editing !== null" heading="Untergruppen bearbeiten" inner-width="max-w-5xl" @close="editing = null">
            <template #actions>
                <a href="#" @click.prevent="store">
                    <ui-sprite src="save" class="text-zinc-400 w-6 h-6" />
                </a>
            </template>
            <div class="flex space-x-3">
                <f-text id="parent-inner_name" v-model="editing.parent.inner_name" label="Interner Name" />
                <f-select id="parent-level" v-model="editing.parent.level" label="Ebene" name="parent-level" :options="meta.levels" />
            </div>
            <div>
                <table cellspacing="0" cellpadding="0" border="0" class="custom-table custom-table-sm table">
                    <thead>
                        <th>NaMi-Name</th>
                        <th>Interner Name</th>
                        <th>Ebene</th>
                        <th>Remote</th>
                    </thead>
                    <tr v-for="child in editing.children" :key="child.id">
                        <td>
                            <span v-text="child.name" />
                        </td>
                        <td>
                            <f-text :id="`inner_name-${child.id}`" v-model="child.inner_name" label="" size="sm" />
                        </td>
                        <td>
                            <f-select :id="`level-${child.id}`" v-model="child.level" label="" size="sm" :name="`level-${child.id}`" :options="meta.levels" />
                        </td>
                        <td>
                            <ui-remote-resource :id="`fileshare-${child.id}`" v-model="child.fileshare" size="sm" label="" />
                        </td>
                    </tr>
                </table>
            </div>
        </ui-popup>
        <form id="groupform" class="grow p-3" @submit.prevent="submit">
            <table cellspacing="0" cellpadding="0" border="0" class="custom-table custom-table-sm table">
                <thead>
                    <th>NaMi-Name</th>
                    <th>Interner Name</th>
                    <th>Ebene</th>
                    <th />
                </thead>

                <template v-for="child in childrenOf('null')" :key="child.id">
                    <tr>
                        <td>
                            <ui-table-toggle-button :value="child" :text="child.name" :level="0" :active="isOpen(child.id)" @toggle="toggle(child)" />
                        </td>
                        <td v-text="child.inner_name" />
                        <td v-text="child.level" />
                        <td>
                            <a v-tooltip="`Bearbeiten`" href="#" class="inline-flex btn btn-warning btn-sm" @click.prevent="edit(child)"><ui-sprite src="pencil" /></a>
                        </td>
                    </tr>
                    <template v-for="subchild in childrenOf(child.id)" :key="subchild.id">
                        <tr>
                            <td>
                                <ui-table-toggle-button :value="subchild" :text="subchild.name" :level="1" :active="isOpen(subchild.id)" @toggle="toggle(subchild)" />
                            </td>
                            <td v-text="subchild.inner_name" />
                            <td v-text="subchild.level" />
                            <td>
                                <a v-if="subchild.children_count" v-tooltip="`Bearbeiten`" href="#" class="inline-flex btn btn-warning btn-sm" @click.prevent="edit(subchild)"><ui-sprite src="pencil" /></a>
                            </td>
                        </tr>
                        <template v-for="subsubchild in childrenOf(subchild.id)" :key="subchild.id">
                            <tr>
                                <td>
                                    <ui-table-toggle-button :value="subsubchild" :text="subsubchild.name" :level="2" :active="isOpen(subsubchild.id)" />
                                </td>
                                <td v-text="subsubchild.inner_name" />
                                <td v-text="subsubchild.level" />
                                <td>
                                    <a v-if="subsubchild.children_count" v-tooltip="`Bearbeiten`" href="#" class="inline-flex btn btn-warning btn-sm" @click.prevent="edit(subsubchild)"><ui-sprite src="pencil" /></a>
                                </td>
                            </tr>
                        </template>
                    </template>
                </template>
            </table>
        </form>
    </page-layout>
</template>

<script lang="js" setup>
import { ref } from 'vue';
import { indexProps, useIndex } from '../../composables/useInertiaApiIndex.js';
import useTableToggle from '../../composables/useTableToggle.js';

const props = defineProps(indexProps);
const { axios, meta, data } = useIndex(props.data, 'invoice');
const { isOpen, toggle, childrenOf } = useTableToggle({ null: data.value });

const editing = ref(null);

async function edit(parent) {
    editing.value = {
        parent: parent,
        children: (await axios.get(parent.links.children)).data.data,
    };
}

async function store() {
    await axios.post(meta.value.links.bulkstore, [editing.value.parent, ...editing.value.children]);
    await toggle(editing.value.parent);
    await toggle(editing.value.parent);
    editing.value = null;
}
</script>
