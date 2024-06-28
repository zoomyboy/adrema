<template>
    <label class="flex flex-col">
        <span v-if="label" class="font-semibold text-gray-400" :class="labelClass(size)">{{ label }}<span v-show="required" class="text-red-800">&nbsp;*</span></span>
        <div class="relative w-full h-full">
            <textarea class="h-full w-full outline-none" :class="[defaultFieldClass, fieldClass(size)]" :rows="rows" @input="trigger" v-text="modelValue"></textarea>
            <div v-if="hint" v-tooltip="hint" class="absolute right-0 top-0 mr-2 mt-2">
                <ui-sprite src="info-button" class="w-5 h-5 text-indigo-200"></ui-sprite>
            </div>
        </div>
    </label>
</template>

<script setup>
import useFieldSize from '../../composables/useFieldSize.js';
const emit = defineEmits(['update:modelValue']);

const {labelClass, fieldClass, defaultFieldClass} = useFieldSize();

const props = defineProps({
    required: {
        type: Boolean,
        default: false,
    },
    size: {
        type: String,
        default: () => 'base',
    },
    rows: {
        type: Number,
        default: () => 4,
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
});
function trigger(v) {
    emit('update:modelValue', v.target.value);
}
</script>
