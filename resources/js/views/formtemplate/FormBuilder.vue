<template>
    <form class="grid gap-3 mt-4 grid-cols-[1fr_max-content] items-start" @submit.prevent="submit">
        <div class="grid gap-3">
            <f-text id="name" v-model="inner.name" name="name" label="Name" required></f-text>
            <ui-box heading="Sektionen" container-class="grid gap-3">
                <template #in-title>
                    <ui-icon-button class="ml-3 btn-primary" icon="plus" @click="addSection">Sektion hinzufügen</ui-icon-button>
                </template>
                <div class="rounded-sm overflow-hidden divide-y divide-primary-600">
                    <section v-for="(section, sindex) in inner.config.sections" :key="sindex">
                        <header class="flex items-center hover:cursor-pointer justify-between bg-primary-700 py-1 px-2 text-primary-200 text-sm" @click="sectionVisible = sindex">
                            <span v-text="section.name ? section.name : '&nbsp;'"></span>
                            <ui-sprite src="chevron" class="w-3 h-3" :class="{'rotate-180': sectionVisible === sindex}"></ui-sprite>
                        </header>
                        <main v-show="sectionVisible === sindex" class="p-1">
                            <f-text :id="`section-${sindex}-name`" v-model="section.name" label="Name" size="sm" :name="`section-${sindex}-name`"></f-text>
                            <f-textarea :id="`section-${sindex}-intro`" v-model="section.intro" label="Einleitung" size="sm" :name="`section-${sindex}-intro`"></f-textarea>
                            <ui-icon-button class="mt-2 btn-primary" icon="plus" @click="addField(section)">Feld hinzufügen</ui-icon-button>
                            <div v-for="(field, findex) in section.fields" :key="`${sindex}-${findex}`" class="grid gap-1">
                                <f-select
                                    :id="`fieldtype-${sindex}-${findex}`"
                                    :model-value="field.type"
                                    :name="`fieldtype-${sindex}-${findex}`"
                                    :options="props.meta.fields"
                                    label="Typ"
                                    size="sm"
                                    @update:modelValue="setFieldType(section, findex, $event)"
                                ></f-select>
                                <template v-if="field.type">
                                    <f-text :id="`section-${sindex}-${findex}-name`" v-model="field.name" label="Name" size="sm" :name="`section-${sindex}-${findex}-name`"></f-text>
                                    <f-switch
                                        :id="`section-${sindex}-${findex}-required`"
                                        v-model="field.required"
                                        label="Erforderlich"
                                        size="sm"
                                        :name="`section-${sindex}-${findex}-required`"
                                        inline
                                    ></f-switch>
                                </template>
                            </div>
                        </main>
                    </section>
                </div>
            </ui-box>
        </div>
        <ui-box heading="Vorschau" container-class="grid gap-3" class="w-[800px]">
            <event-form style="--primary: yellow; --secondary: green; --font: #ff6600; --circle: #111111" :value="previewString"></event-form>
        </ui-box>
    </form>
</template>

<script setup>
import {computed, ref} from 'vue';
import '!/eventform/dist/main.js';

const sectionVisible = ref(-1);

const props = defineProps({
    modelValue: {},
    meta: {},
});

const inner = ref(JSON.parse(JSON.stringify(props.modelValue)));

function addSection() {
    sectionVisible.value = inner.value.config.sections.push({...props.meta.section_default}) - 1;
}

function addField(section) {
    section.fields = [...section.fields, {name: null, type: null}];
}

function setFieldType(section, findex, type) {
    if (type === null) {
        section.fields[findex].type = null;
        return;
    }
    section.fields[findex] = {...props.meta.fields.find((f) => f.id === type).default};
}

const previewString = computed(() => (inner.value && inner.value ? JSON.stringify(inner.value.config) : '{}'));
</script>
