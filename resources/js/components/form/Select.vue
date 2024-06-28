<template>
    <label class="flex flex-col group" :for="id" :class="sizeClass(size)">
        <f-label v-if="label" :required="required" :value="label"></f-label>
        <div class="relative flex-none flex">
            <select v-model="inner" :disabled="disabled" :name="name" :class="[fieldHeight, fieldAppearance]" class="py-0 pr-8 group-[.field-base]:pl-2 group-[.field-sm]:pl-1 w-full">
                <option v-if="placeholder" :value="def">{{ placeholder }}</option>
                <option v-for="option in parsedOptions" :key="option.id" :value="option.id">{{ option.name }}</option>
            </select>
            <f-hint v-if="hint" :value="hint"></f-hint>
        </div>
    </label>
</template>

<script setup>
import {computed, ref} from 'vue';
import useFieldSize from '../../composables/useFieldSize.js';
import map from 'lodash/map';

const {fieldHeight, fieldAppearance, sizeClass} = useFieldSize();

const emit = defineEmits(['update:modelValue']);

const props = defineProps({
    nullValue: {
        required: false,
        default: () => null,
    },
    disabled: {
        type: Boolean,
        default: function () {
            return false;
        },
    },
    id: {},
    inset: {
        type: Boolean,
        default: false,
    },
    size: {
        default: function () {
            return 'base';
        },
    },
    emptyLabel: {
        default: false,
        type: Boolean,
    },
    modelValue: {
        default: undefined,
    },
    label: {
        default: null,
    },
    required: {
        type: Boolean,
        default: false,
    },
    placeholder: {
        default: '--kein--',
        type: String,
    },
    def: {
        required: false,
        type: Number,
        default: -1,
    },
    name: {
        required: true,
    },
    hint: {},
    options: {
        default: function () {
            return [];
        },
    },
});

const parsedOptions = computed(() => {
    return Array.isArray(props.options)
        ? props.options
        : map(props.options, (value, key) => {
              return {name: value, id: key};
          });
});

const def = ref('iu1Feixah5AeKai3ewooJahjeaegee0eiD4maeth1oul4Hei7u');

const inner = computed({
    get: () => {
        return props.modelValue === props.nullValue ? def.value : props.modelValue;
    },
    set: (v) => {
        emit('update:modelValue', v === def.value ? props.nullValue : v);
    },
});
</script>
