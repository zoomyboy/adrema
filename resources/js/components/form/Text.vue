<template>
    <label class="flex flex-col group" :for="id" :class="sizes[size]">
        <span v-if="label" class="font-semibold leading-none text-gray-400 group-[.field-base]:text-sm group-[.field-sm]:text-xs">
            {{ label }}
            <span v-show="required" class="text-red-800">&nbsp;*</span>
        </span>
        <div class="relative flex-none flex">
            <input
                :id="id"
                :type="type"
                :value="transformedValue"
                :disabled="disabled"
                placeholder=""
                :min="min"
                :max="max"
                class="group-[.field-base]:h-[35px] group-[.field-sm]:h-[23px] group-[.field-base]:border-2 group-[.field-sm]:border border-gray-600 border-solid text-gray-300 bg-gray-700 leading-none rounded-lg group-[.field-base]:text-sm group-[.field-sm]:text-xs py-0 group-[.field-base]:px-2 group-[.field-sm]:px-1 w-full"
                @input="onInput"
                @change="onChange"
                @focus="focus = true"
                @blur="focus = false"
            />
            <div v-if="hint" class="h-full items-center flex absolute top-0 right-0">
                <div v-tooltip="hint">
                    <ui-sprite src="info-button" class="text-primary-700"></ui-sprite>
                </div>
            </div>
        </div>
    </label>
</template>

<script setup>
import wNumb from 'wnumb';
import {ref, computed} from 'vue';

const emit = defineEmits(['update:modelValue']);

var numb = {
    natural: wNumb({
        mark: '',
        thousand: '',
        decimals: 0,
        decoder: (a) => a * 100,
        encoder: (a) => a / 100,
    }),
    area: wNumb({
        mark: ',',
        thousand: '',
        decimals: 2,
        decoder: (a) => a * 100,
        encoder: (a) => a / 100,
    }),
};

var transformers = {
    none: {
        display: {
            to: (v) => v,
            from: (v) => v,
        },
        edit: {
            to: (v) => v,
            from: (v) => v,
        },
    },
    area: {
        display: {
            to: (v) => (v === null ? '' : numb.area.to(v)),
            from: (v) => (v === '' ? null : numb.area.from(v)),
        },
        edit: {
            to(v) {
                if (v === null) {
                    return '';
                }
                if (Math.round(v / 100) * 100 === v) {
                    return numb.natural.to(v);
                }
                return numb.area.to(v);
            },
            from(v) {
                if (v === '') {
                    return null;
                }
                if (v.indexOf(',') === -1) {
                    return numb.natural.from(v);
                }

                return numb.area.from(v);
            },
        },
    },
};

const props = defineProps({
    mode: {
        type: String,
        default: () => 'none',
    },
    required: {
        type: Boolean,
        default: () => false,
    },
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
        type: String,
        required: true,
    },
    label: {
        type: String,
        default: () => '',
    },
    type: {
        type: String,
        default: () => 'text',
    },
    disabled: {
        default: () => false,
        type: Boolean,
    },
    min: {
        type: Number,
        default: () => undefined,
    },
    max: {
        type: Number,
        default: () => undefined,
    },
});

const focus = ref(false);
const sizes = ref({
    sm: 'field-sm',
    base: 'field-base',
    lg: 'field-lg',
});

const transformedValue = computed({
    get: () => transformers[props.mode][focus.value ? 'edit' : 'display'].to(props.modelValue),
    set: (v) => emit('update:modelValue', transformers[props.mode][focus.value ? 'edit' : 'display'].from(v)),
});
function onChange(v) {
    if (props.mode !== 'none') {
        transformedValue.value = v.target.value;
    }
}
function onInput(v) {
    if (props.mode === 'none') {
        transformedValue.value = v.target.value;
    }
}
</script>
