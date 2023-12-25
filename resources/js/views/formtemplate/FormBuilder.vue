<template>
    <ui-popup :heading="`Vorlage ${inner.id ? 'bearbeiten' : 'erstellen'}`" full @close="$emit('cancel')">
        <ui-popup v-if="singleField !== null && singleField.model === null" heading="Feldtyp auswählen" @close="singleField = null">
            <div class="mt-3 grid gap-3 grid-cols-3">
                <a v-for="(field, index) in props.meta.fields" :key="index" class="py-2 px-3 border rounded bg-zinc-800 hover:bg-zinc-700 transition" href="#" @click.prevent="setFieldType(field)">
                    <span v-text="field.name"></span>
                </a>
            </div>
        </ui-popup>
        <template #actions>
            <a href="#" @click.prevent="$emit('submit', inner)">
                <ui-sprite src="save" class="text-zinc-400 w-6 h-6"></ui-sprite>
            </a>
        </template>
        <form class="grid gap-3 mt-4 grid-cols-[1fr_max-content] items-start" @submit.prevent="submit">
            <div class="grid gap-3">
                <f-text id="name" v-model="inner.name" name="name" label="Name" required></f-text>
                <asideform v-if="singleSection !== null" :heading="`Sektion ${singleSection.index !== null ? 'bearbeiten' : 'erstellen'}`" @close="singleSection = null" @submit="storeSection">
                    <f-text :id="`sectionform-name`" v-model="singleSection.model.name" label="Name" :name="`sectionform-name`"></f-text>
                    <f-textarea :id="`sectionform-intro`" v-model="singleSection.model.intro" label="Einleitung" :name="`sectionform-intro`"></f-textarea>
                </asideform>
                <asideform
                    v-if="singleField !== null && singleField.model !== null"
                    :heading="`Feld ${singleField.index !== null ? 'bearbeiten' : 'erstellen'}`"
                    @close="singleField = null"
                    @submit="storeField"
                >
                    <f-text id="fieldname" v-model="singleField.model.name" label="Name" size="sm" name="fieldname"></f-text>
                    <f-switch id="fieldrequired" v-model="singleField.model.required" label="Erforderlich" size="sm" name="fieldrequired" inline></f-switch>
                </asideform>
            </div>
            <ui-box heading="Vorschau" container-class="grid gap-3" class="w-[800px]">
                <event-form
                    editable
                    style="--primary: hsl(181, 75%, 26%); --secondary: hsl(181, 75%, 35%); --font: hsl(181, 84%, 78%); --circle: hsl(181, 86%, 16%)"
                    :value="previewString"
                    @editSection="editSection($event.detail[0])"
                    @addSection="addSection"
                    @addField="addField($event.detail[0])"
                    @editField="editField($event.detail[0], $event.detail[1])"
                    @deleteField="deleteField($event.detail[0], $event.detail[1])"
                ></event-form>
            </ui-box>
        </form>
    </ui-popup>
</template>

<script setup>
import {computed, ref} from 'vue';
import '!/eventform/dist/main.js';
import Asideform from './Asideform.vue';

const sectionVisible = ref(-1);
const singleSection = ref(null);
const singleField = ref(null);

const props = defineProps({
    modelValue: {},
    meta: {},
});
const emit = defineEmits(['submit', 'cancel']);

function editSection(sectionIndex) {
    singleSection.value = {
        model: {...inner.value.config.sections[sectionIndex]},
        index: sectionIndex,
    };
}

const inner = ref(JSON.parse(JSON.stringify(props.modelValue)));
const innerMeta = ref(JSON.parse(JSON.stringify(props.meta)));

function addSection() {
    singleSection.value = {
        model: JSON.parse(JSON.stringify(innerMeta.value.section_default)),
        index: null,
    };
}

function editField(sindex, findex) {
    singleField.value = {
        model: JSON.parse(JSON.stringify(inner.value.config.sections[sindex].fields[findex])),
        sectionIndex: sindex,
        index: findex,
    };
}

function storeSection() {
    if (singleSection.value.index !== null) {
        inner.value.config.sections.splice(singleSection.value.index, 1, singleSection.value.model);
    } else {
        inner.value.config.sections.push(singleSection.value.model);
    }
    singleSection.value = null;
}

function storeField() {
    if (singleField.value.index !== null) {
        inner.value.config.sections[singleField.value.sectionIndex].fields.splice(singleField.value.index, 1, singleField.value.model);
    } else {
        inner.value.config.sections[singleField.value.sectionIndex].fields.push(singleField.value.model);
    }
    singleField.value = null;
}

function deleteField(sindex, findex) {
    inner.value.config.sections[sindex].fields.splice(findex, 1);
}

function addField(sectionIndex) {
    singleField.value = {
        model: null,
        sectionIndex: sectionIndex,
        index: null,
    };
}

function setFieldType(field) {
    singleField.value.model = JSON.parse(JSON.stringify(field.default));
}

const previewString = computed(() => (inner.value && inner.value ? JSON.stringify(inner.value.config) : '{}'));
</script>
