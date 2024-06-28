<template>
    <label class="flex flex-col group" :for="id" :class="sizes[size]">
        <span v-if="label" class="font-semibold leading-none text-gray-400 group-[.field-base]:text-sm group-[.field-sm]:text-xs">
            {{ label }}
            <span v-show="required" class="text-red-800">&nbsp;*</span>
        </span>
        <div class="relative flex-none flex">
            <select
                v-model="inner"
                :disabled="disabled"
                :name="name"
                class="group-[.field-base]:h-[35px] group-[.field-sm]:h-[23px] group-[.field-base]:border-2 group-[.field-sm]:border border-gray-600 border-solid text-gray-300 bg-gray-700 leading-none rounded-lg group-[.field-base]:text-sm group-[.field-sm]:text-xs py-0 pr-8 group-[.field-base]:pl-2 group-[.field-sm]:pl-1 w-full"
            >
                <option v-if="placeholder" :value="def">{{ placeholder }}</option>

                <option v-for="option in parsedOptions" :key="option.id" :value="option.id">{{ option.name }}</option>
            </select>
            <div class="h-full items-center flex absolute top-0 right-0">
                <div v-if="hint" v-tooltip="hint">
                    <ui-sprite src="info-button" class="info-button"></ui-sprite>
                </div>
            </div>
        </div>
    </label>
</template>

<script setup>
import {computed, ref} from 'vue';
import map from 'lodash/map';

const sizes = ref({
    sm: 'field-sm',
    base: 'field-base',
    lg: 'field-lg',
});

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
