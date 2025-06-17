<template>
    <ui-popup v-if="selecting !== false" heading="Resource auswählen" @close="selecting = false">
        <ui-remote-selector :value="selecting" @input="set" />
    </ui-popup>
    <label class="flex flex-col group" :for="id" :class="sizeClass(size)">
        <f-label v-if="label" :required="false" :value="label" />
        <div class="relative flex-none flex">
            <div class="w-full flex flex-col justify-center" :class="[fieldHeight, fieldAppearance, paddingX]" @click.prevent="selecting = modelValue === null ? null : {...modelValue}">
                <div v-if="modelValue !== null" v-text="modelValue.resource" />
                <div v-else>Datei auswählen</div>
            </div>
            <f-hint v-if="hint" :value="hint" />
        </div>
    </label>
</template>

<script setup>
import useFieldSize from '../../composables/useFieldSize';
import {ref} from 'vue';

const emit = defineEmits(['update:modelValue']);
const {fieldHeight, fieldAppearance, paddingX, sizeClass} = useFieldSize();

const selecting = ref(false);

function set(resource) {
    emit('update:modelValue', resource);
    selecting.value = false;
}

const props = defineProps({
    size: {
        type: String,
        default: () => 'base',
    },
    id: {
        type: String,
        required: true,
    },
    hint: {
        type: String,
        default: () => '',
    },
    modelValue: {
        validator: (v) => typeof v === 'object' || v === null,
        required: true,
    },
    label: {
        type: String,
        default: () => '',
    },
});
</script>
