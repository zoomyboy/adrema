<template>
    <label class="field-wrap" :for="id" :class="`field-wrap-${size}`">
        <span v-if="label" class="field-label">
            {{ label }}
            <span v-show="required" class="text-red-800">&nbsp;*</span>
        </span>
        <div class="real-field-wrap" :class="`size-${size}`">
            <select v-model="inner" :disabled="disabled" :name="name">
                <option v-if="placeholder" :value="def">{{ placeholder }}</option>

                <option v-for="option in parsedOptions" :key="option.id" :value="option.id">{{ option.name }}</option>
            </select>
            <div class="info-wrap">
                <div v-if="hint" v-tooltip="hint">
                    <ui-sprite src="info-button" class="info-button"></ui-sprite>
                </div>
                <div v-if="size != 'xs'" class="px-1 relative">
                    <ui-sprite class="chevron w-3 h-3 fill-current" src="chevron"></ui-sprite>
                </div>
                <div v-if="size == 'xs'" class="px-1 relative">
                    <ui-sprite class="chevron w-2 h-2 fill-current" src="chevron"></ui-sprite>
                </div>
            </div>
        </div>
    </label>
</template>

<script setup>
import {computed, ref} from 'vue';
import map from 'lodash/map';

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
