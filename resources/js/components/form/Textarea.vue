<template>
    <label class="flex flex-col group" :for="id" :class="sizeClass(size)">
        <f-label v-if="label" :required="required" :value="label"></f-label>
        <div class="relative flex-none flex">
            <textarea class="w-full h-full" :class="[fieldAppearance, paddingX, paddingY]" :rows="rows" @input="trigger" v-text="modelValue"></textarea>
            <f-hint v-if="hint" :value="hint"></f-hint>
        </div>
    </label>
</template>

<script setup>
import useFieldSize from '../../composables/useFieldSize.js';
const emit = defineEmits(['update:modelValue']);

const {fieldAppearance, paddingX, paddingY, sizeClass} = useFieldSize();

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
