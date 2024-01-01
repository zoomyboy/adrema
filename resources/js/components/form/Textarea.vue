<template>
    <label class="flex flex-col">
        <span v-if="label && !inset" class="font-semibold text-gray-400" :class="{
            'text-xs': size == 'sm',
            'text-sm': size === null,
        }">{{ label }}<span v-show="required" class="text-red-800">&nbsp;*</span></span>
        <span v-if="label && inset" class="absolute top-0 left-0 -mt-2 px-1 ml-3 inset-bg font-semibold text-gray-700"
            :class="{
                'text-xs': size == 'sm',
                'text-sm': size === null,
            }">{{ label }}<span v-show="required" class="text-red-800">&nbsp;*</span></span>
        <div class="relative w-full h-full">
            <textarea :placeholder="placeholder" class="h-full w-full outline-none bg-gray-700 border-gray-600 border-solid"
                :rows="rows" :class="{
                    'rounded-lg text-sm border-2 p-2 text-gray-300': size === null,
                    'rounded-lg py-2 px-2 text-xs border-2 text-gray-300': size == 'sm',
                }" @input="trigger" v-text="modelValue"></textarea>
            <div v-if="hint" v-tooltip="hint" class="absolute right-0 top-0 mr-2 mt-2">
                <ui-sprite src="info-button" class="w-5 h-5 text-indigo-200"></ui-sprite>
            </div>
        </div>
    </label>
</template>

<script setup>
const emit = defineEmits(['update:modelValue']);

const props = defineProps({
    required: {
        type: Boolean,
        default: false,
    },
    inset: {
        default: false,
        type: Boolean,
    },
    size: {
        default: null,
    },
    rows: {
        default: function () {
            return 4;
        },
    },
    id: {
        required: true,
    },
    hint: {
        default: null,
    },
    modelValue: {
        default: undefined,
    },
    mask: {
        default: undefined,
    },
    label: {
        default: false,
    },
    type: {
        required: false,
        default: function () {
            return 'text';
        },
    },
    placeholder: {
        default: '',
    },
});
function trigger(v) {
    emit('update:modelValue', v.target.value);
}
if (typeof props.modelValue === 'undefined') {
    emit('update:modelValue', '');
}
</script>

<style scope>
.inset-bg {
    background: linear-gradient(to bottom, hsl(247.5, 66.7%, 97.6%) 0%, hsl(247.5, 66.7%, 97.6%) 41%, hsl(0deg 0% 100%) 41%, hsl(180deg 0% 100%) 100%);
}
</style>
