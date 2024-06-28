<template>
    <label class="flex flex-col group" :for="id" :class="sizes[size]">
        <f-label :required="required" :value="label"></f-label>
        <div class="relative flex-none flex">
            <input
                :id="id"
                :type="type"
                :value="transformedValue"
                :disabled="disabled"
                placeholder=""
                :min="min"
                :max="max"
                :class="[fieldHeight, fieldAppearance, paddingX]"
                class="w-full"
                @input="onInput"
                @change="onChange"
                @focus="focus = true"
                @blur="focus = false"
            />
            <f-hint v-if="hint" :value="hint"></f-hint>
        </div>
    </label>
</template>

<script setup>
import wNumb from 'wnumb';
import {ref, computed} from 'vue';
import useFieldSize from '../../composables/useFieldSize';

const {fieldHeight, fieldAppearance, paddingX} = useFieldSize();

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
        validator: (v) => typeof v === 'string' || v === null,
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
