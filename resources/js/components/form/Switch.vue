<template>
    <label class="flex flex-col items-start group" :for="id" :class="sizeClass(size)">
        <f-label v-if="label" :required="false" :value="label"></f-label>
        <span class="relative flex-none flex" :class="{'pr-8': hint, [fieldHeight]: true}">
            <input :id="id" v-model="v" type="checkbox" :name="name" :value="value" :disabled="disabled" class="absolute peer opacity-0" @keypress="$emit('keypress', $event)" />
            <span
                class="relative cursor-pointer h-full rounded peer-focus:bg-red-500 transition-all duration-300 group-[.field-base]:w-[70px] group-[.field-sm]:w-[46px] bg-gray-700 peer-checked:bg-primary-700"
            ></span>
            <span class="absolute h-full top-0 left-0 flex-none flex justify-center items-center aspect-square">
                <ui-sprite
                    class="relative text-gray-400 flex-none text-white duration-300 group-[.field-base]:w-3 group-[.field-base]:h-3 group-[.field-sm]:w-2 group-[.field-sm]:h-2"
                    src="check"
                ></ui-sprite
            ></span>
            <span class="absolute h-full top-0 group-[.field-base]:left-[35px] group-[.field-sm]:left-[23px] flex-none flex justify-center items-center aspect-square">
                <ui-sprite
                    class="relative text-gray-400 flex-none text-white duration-300 group-[.field-base]:w-3 group-[.field-base]:h-3 group-[.field-sm]:w-2 group-[.field-sm]:h-2"
                    src="close"
                ></ui-sprite
            ></span>
            <var
                class="absolute transition-all duration-300 bg-gray-400 rounded group-[.field-base]:top-[3px] group-[.field-base]:left-[3px] group-[.field-sm]:top-[2px] group-[.field-sm]:left-[2px] group-[.field-base]:w-[29px] group-[.field-sm]:w-[19px] group-[.field-base]:h-[29px] group-[.field-sm]:h-[19px] group-[.field-base]:peer-checked:left-[37px] group-[.field-sm]:peer-checked:left-[25px]"
            ></var>
            <f-hint v-if="hint" :value="hint"></f-hint>
        </span>
    </label>
</template>

<script setup>
import {computed} from 'vue';
import useFieldSize from '../../composables/useFieldSize.js';

const {sizeClass, fieldHeight} = useFieldSize();

const emit = defineEmits(['update:modelValue', 'keypress']);

const props = defineProps({
    hint: {
        type: String,
        default: () => '',
    },
    size: {
        type: String,
        default: () => 'base',
    },
    id: {
        type: String,
        required: true,
    },
    name: {
        required: true,
        type: String,
    },
    disabled: {
        type: Boolean,
        default: () => false,
    },
    value: {
        validator: (v) => true,
        default: () => undefined,
    },
    label: {
        type: String,
        default: () => '',
    },
    modelValue: {
        validator: (v) => true,
        default: () => undefined,
    },
});

const v = computed({
    set: (v) => {
        if (props.disabled === true) {
            return;
        }

        if (typeof props.modelValue === 'boolean') {
            emit('update:modelValue', v);
            return;
        }

        var a = props.modelValue.filter((i) => i !== props.value);
        if (v) {
            a.push(props.value);
        }

        emit('update:modelValue', a);
    },
    get() {
        if (typeof props.modelValue === 'boolean') {
            return props.modelValue;
        }

        if (typeof props.modelValue === 'undefined') {
            return emit('update:modelValue', false);
        }

        return props.modelValue.indexOf(props.value) !== -1;
    },
});
</script>
