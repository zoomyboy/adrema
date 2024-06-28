<template>
    <form class="grid gap-3 mt-4 grid-cols-[1fr_max-content] items-start" @submit.prevent="submit">
        <div class="grid gap-3">
            <slot name="meta"></slot>
            <asideform
                v-if="singleSection !== null && singleSection.mode === 'edit'"
                :heading="`Sektion ${singleSection.index !== null ? 'bearbeiten' : 'erstellen'}`"
                @close="singleSection = null"
                @submit="storeSection"
            >
                <f-text id="sectionform-name" v-model="singleSection.model.name" label="Name"></f-text>
                <f-textarea id="sectionform-intro" v-model="singleSection.model.intro" label="Einleitung"></f-textarea>
            </asideform>
            <asideform v-if="singleSection !== null && singleSection.mode === 'reorder'" heading="Felder ordnen" @close="singleSection = null" @submit="storeSection">
                <draggable v-model="singleSection.model.fields" item-key="key" :component-data="{class: 'mt-3 grid gap-3'}">
                    <template #item="{element}">
                        <div class="py-2 px-3 border rounded bg-zinc-800 hover:bg-zinc-700 transition justify-between flex items-center">
                            <span v-text="element.name"></span>
                            <a href="#">
                                <ui-sprite v-tooltip="`Umordnen`" class="w-5 h-5" src="menu"></ui-sprite>
                            </a>
                        </div>
                    </template>
                </draggable>
            </asideform>
            <asideform v-if="singleSection === null && singleField === null" heading="Feld erstellen" :closeable="false" :storeable="false" @submit="storeField">
                <div class="mt-3 grid gap-3">
                    <a v-for="(field, index) in props.meta.fields" :key="index" class="py-2 px-3 border rounded bg-zinc-800 hover:bg-zinc-700 transition" href="#" @click.prevent="addField(field)">
                        <span v-text="field.name"></span>
                    </a>
                </div>
            </asideform>
            <asideform
                v-if="singleSection === null && singleField !== null"
                :heading="`Feld ${singleField.index !== null ? 'bearbeiten' : 'erstellen'}`"
                @close="singleField = null"
                @submit="storeField"
            >
                <f-text id="fieldname" v-model="singleField.model.name" label="Name" size="sm"></f-text>
                <f-textarea id="intro" v-model="singleField.model.intro" label="Einleitung" size="sm"></f-textarea>
                <column-selector v-model="singleField.model.columns"></column-selector>
                <component :is="fields[singleField.model.type]" v-model="singleField.model" :payload="inner.sections" :meta="props.meta"></component>
                <f-select id="nami_type" v-model="singleField.model.nami_type" :options="meta.namiTypes" label="NaMi-Feld" size="sm" name="nami_type"></f-select>
                <f-select id="special_type" v-model="singleField.model.special_type" :options="meta.specialTypes" label="Bedeutung" size="sm" name="special_type"></f-select>
                <f-textarea id="hint" v-model="singleField.model.hint" label="Hinweis" size="sm"></f-textarea>
                <f-switch
                    v-show="singleField.model.nami_type === null"
                    id="for_members"
                    v-model="singleField.model.for_members"
                    label="Für Unter-Mitglieder zusätzlich abfragen"
                    size="sm"
                    name="for_members"
                ></f-switch>
            </asideform>
        </div>
        <ui-box heading="Vorschau" container-class="grid gap-3" class="w-[800px]">
            <event-form
                editable
                style="--primary: hsl(181, 75%, 26%); --secondary: hsl(181, 75%, 35%); --font: hsl(181, 84%, 78%); --circle: hsl(181, 86%, 16%)"
                :base-url="meta.base_url"
                :value="previewString"
                @editSection="editSection($event.detail[0])"
                @addSection="addSection"
                @editField="editField($event.detail[0], $event.detail[1])"
                @deleteField="deleteField($event.detail[0], $event.detail[1])"
                @editFields="startReordering($event.detail[0])"
                @deleteSection="deleteSection($event.detail[0])"
                @active="updateActive($event.detail[0])"
            ></event-form>
        </ui-box>
    </form>
</template>

<script setup>
import {watch, computed, ref} from 'vue';
import {snakeCase} from 'change-case';
import '!/adrema-form/dist/main.js';
import Asideform from './Asideform.vue';
import TextareaField from './TextareaField.vue';
import TextField from './TextField.vue';
import DateField from './DateField.vue';
import DropdownField from './RadioField.vue';
import RadioField from './RadioField.vue';
import GroupField from './GroupField.vue';
import NumberField from './NumberField.vue';
import CheckboxField from './CheckboxField.vue';
import CheckboxesField from './CheckboxesField.vue';
import ColumnSelector from './ColumnSelector.vue';
import Draggable from 'vuedraggable';

const singleSection = ref(null);
const singleField = ref(null);
const active = ref(null);

async function onReorder() {
    var order = this.inner.map((f) => f.id);
    this.loading = true;
    This.inner = (await this.axios.patch(`/mediaupload/${this.parentName}/${this.parentId}/${this.collection}`, {order})).data;
    this.loading = false;
}

function updateActive(a) {
    active.value = a;
    if (a === null) {
        addSection();
    }
}

const props = defineProps({
    modelValue: {},
    meta: {},
});
const emit = defineEmits(['update:modelValue']);

const fields = {
    TextareaField: TextareaField,
    TextField: TextField,
    DateField: DateField,
    DropdownField: DropdownField,
    RadioField: RadioField,
    CheckboxField: CheckboxField,
    CheckboxesField: CheckboxesField,
    GroupField: GroupField,
    NumberField: NumberField,
};

function editSection(sectionIndex) {
    singleSection.value = {
        model: {...inner.value.sections[sectionIndex]},
        index: sectionIndex,
        mode: 'edit',
    };
}

function startReordering(index) {
    singleSection.value = {
        model: {...inner.value.sections[index]},
        index: index,
        mode: 'reorder',
    };
}

function push() {
    emit('update:modelValue', inner.value);
}

const inner = ref(JSON.parse(JSON.stringify(props.modelValue)));
const innerMeta = ref(JSON.parse(JSON.stringify(props.meta)));

function addSection() {
    singleSection.value = {
        model: JSON.parse(JSON.stringify(innerMeta.value.section_default)),
        index: null,
        mode: 'edit',
    };
}

function editField(sindex, findex) {
    singleField.value = {
        model: JSON.parse(JSON.stringify(inner.value.sections[sindex].fields[findex])),
        sectionIndex: sindex,
        index: findex,
    };
}

function storeSection() {
    if (singleSection.value.index !== null) {
        inner.value.sections.splice(singleSection.value.index, 1, singleSection.value.model);
    } else {
        inner.value.sections.push(singleSection.value.model);
    }
    singleSection.value = null;
    push();
}

function storeField() {
    singleField.value.model.key = snakeCase(singleField.value.model.name);
    if (singleField.value.index !== null) {
        inner.value.sections[singleField.value.sectionIndex].fields.splice(singleField.value.index, 1, singleField.value.model);
    } else {
        inner.value.sections[singleField.value.sectionIndex].fields.push(singleField.value.model);
    }
    singleField.value = null;
    push();
}

function deleteField(sindex, findex) {
    inner.value.sections[sindex].fields.splice(findex, 1);
    push();
}

function addField(type) {
    singleField.value = {
        model: JSON.parse(JSON.stringify(type.default)),
        sectionIndex: active,
        index: null,
    };
}

function deleteSection(sindex) {
    inner.value.sections.splice(sindex, 1);
    push();
}

const previewString = computed(() => (inner.value && inner.value ? JSON.stringify(inner.value) : '{}'));
</script>
