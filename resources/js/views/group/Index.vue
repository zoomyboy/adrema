<template>
    <page-layout>
        <template #right>
            <f-save-button form="groupform"></f-save-button>
        </template>
        <ui-popup v-if="editing !== null" heading="Untergruppen bearbeiten" inner-width="max-w-5xl" @close="editing = null">
            <template #actions>
                <a href="#" @click.prevent="store">
                    <ui-sprite src="save" class="text-zinc-400 w-6 h-6"></ui-sprite>
                </a>
            </template>
            <div class="flex space-x-3">
                <f-text id="parent-inner_name" v-model="editing.parent.inner_name" label="Interner Name" name="parent-inner_name"></f-text>
                <f-select id="parent-level" v-model="editing.parent.level" label="Ebene" name="parent-level" :options="meta.levels"></f-select>
            </div>
            <div>
                <table cellspacing="0" cellpadding="0" border="0" class="custom-table custom-table-sm table">
                    <thead>
                        <th>NaMi-Name</th>
                        <th>Interner Name</th>
                        <th>Ebene</th>
                    </thead>
                    <tr v-for="child in editing.children" :key="child.id">
                        <td>
                            <span v-text="child.name"></span>
                        </td>
                        <td>
                            <f-text :id="`inner_name-${child.id}`" v-model="child.inner_name" label="" size="sm" :name="`inner_name-${child.id}`"></f-text>
                        </td>
                        <td>
                            <f-select :id="`level-${child.id}`" v-model="child.level" label="" size="sm" :name="`level-${child.id}`" :options="meta.levels"></f-select>
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
                    <th></th>
                </thead>

                <template v-for="child in childrenOf('null')" :key="child.id">
                    <tr>
                        <td>
                            <div class="flex space-x-2">
                                <a v-if="!isOpen(child.id)" v-tooltip="`Öffnen`" href="#" class="inline-flex btn btn-info btn-sm" @click.prevent="open(child)"><ui-sprite src="plus"></ui-sprite></a>
                                <a v-if="isOpen(child.id)" v-tooltip="`Schließen`" href="#" class="inline-flex btn btn-info btn-sm" @click.prevent="close(child)"
                                    ><ui-sprite src="close"></ui-sprite
                                ></a>
                                <span v-text="child.name"></span>
                            </div>
                        </td>
                        <td v-text="child.inner_name"></td>
                        <td v-text="child.level"></td>
                        <td>
                            <a v-tooltip="`Bearbeiten`" href="#" class="inline-flex btn btn-warning btn-sm" @click.prevent="edit(child)"><ui-sprite src="pencil"></ui-sprite></a>
                        </td>
                    </tr>
                    <template v-for="subchild in childrenOf(child.id)" :key="subchild.id">
                        <tr>
                            <div class="pl-12 flex space-x-2">
                                <a v-if="subchild.children_count !== 0 && !isOpen(subchild.id)" v-tooltip="`Öffnen`" href="#" class="inline-flex btn btn-info btn-sm" @click.prevent="open(subchild)"
                                    ><ui-sprite src="plus"></ui-sprite
                                ></a>
                                <a v-if="subchild.children_count !== 0 && isOpen(subchild.id)" v-tooltip="`Schließen`" href="#" class="inline-flex btn btn-info btn-sm" @click.prevent="close(subchild)"
                                    ><ui-sprite src="close"></ui-sprite
                                ></a>
                                <span v-text="subchild.name"></span>
                            </div>
                            <td v-text="subchild.inner_name"></td>
                            <td v-text="subchild.level"></td>
                            <td>
                                <a v-if="subchild.children_count" v-tooltip="`Bearbeiten`" href="#" class="inline-flex btn btn-warning btn-sm" @click.prevent="edit(subchild)"
                                    ><ui-sprite src="pencil"></ui-sprite
                                ></a>
                            </td>
                        </tr>
                        <template v-for="subsubchild in childrenOf(subchild.id)" :key="subchild.id">
                            <tr>
                                <div class="pl-24 flex space-x-2">
                                    <a
                                        v-if="subsubchild.children_count !== 0 && !isOpen(subsubchild.id)"
                                        v-tooltip="`Öffnen`"
                                        href="#"
                                        class="inline-flex btn btn-info btn-sm"
                                        @click.prevent="open(subsubchild)"
                                        ><ui-sprite src="plus"></ui-sprite
                                    ></a>
                                    <a
                                        v-if="subsubchild.children_count !== 0 && isOpen(subsubchild.id)"
                                        v-tooltip="`Schließen`"
                                        href="#"
                                        class="inline-flex btn btn-info btn-sm"
                                        @click.prevent="close(subsubchild)"
                                        ><ui-sprite src="close"></ui-sprite
                                    ></a>
                                    <span v-text="subsubchild.name"></span>
                                </div>
                                <td v-text="subchild.inner_name"></td>
                                <td v-text="subchild.level"></td>
                                <td>
                                    <a v-if="subsubchild.children_count" v-tooltip="`Bearbeiten`" href="#" class="inline-flex btn btn-warning btn-sm" @click.prevent="edit(subsubchild)"
                                        ><ui-sprite src="pencil"></ui-sprite
                                    ></a>
                                </td>
                            </tr>
                        </template>
                    </template>
                </template>
            </table>
        </form>
    </page-layout>
</template>

<script setup>
import {computed, ref, reactive} from 'vue';
import {indexProps, useIndex} from '../../composables/useInertiaApiIndex.js';
const props = defineProps(indexProps);
var {axios, meta, data} = useIndex(props.data, 'invoice');

const children = reactive({
    null: data.value,
});

var editing = ref(null);

async function open(parent) {
    const result = (await axios.get(parent.links.children)).data;

    children[parent.id] = result.data;
}

async function edit(parent) {
    editing.value = {
        parent: parent,
        children: (await axios.get(parent.links.children)).data.data,
    };
}

function close(parent) {
    delete children[parent.id];
}

function isOpen(child) {
    return child in children;
}

function childrenOf(parent) {
    return children[parent] ? children[parent] : [];
}

async function store() {
    await axios.post(meta.value.links.bulkstore, [editing.value.parent, ...editing.value.children]);
    children[editing.value.parent.id] = (await axios.get(editing.value.parent.links.children)).data.data;
    editing.value = null;
}
</script>
