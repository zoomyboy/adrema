<template>
    <label class="flex flex-col group" :for="id" :class="sizeClass(size)">
        <f-label v-if="label" :required="false" :value="label"></f-label>
        <div class="relative flex-none flex">
            <div
                :class="[fieldHeight, fieldAppearance, selectAppearance]"
                class="form-select flex items-center w-full"
                @click="visible = !visible"
                v-text="`${modelValue.length} Einträge ausgewählt`"
            ></div>
            <div v-show="visible" class="absolute w-[max-content] z-30 max-h-[25rem] overflow-auto shadow-lg bg-gray-600 border border-gray-500 rounded-lg p-2 top-7 space-y-1">
                <div v-for="(option, index) in parsedOptions" :key="index" class="flex items-center space-x-2">
                    <f-switch :id="`${id}-${index}`" size="sm" :model-value="modelValue.includes(option.id)" :value="option.id" @update:modelValue="trigger(option, $event)"></f-switch>
                    <div class="text-sm text-gray-200" v-text="option.name"></div>
                </div>
            </div>
        </div>
    </label>
</template>

<script setup>
import map from 'lodash/map';
import {ref, computed} from 'vue';
import useFieldSize from '../../composables/useFieldSize';

const {fieldHeight, fieldAppearance, paddingX, sizeClass, selectAppearance} = useFieldSize();

const emit = defineEmits(['update:modelValue']);

const props = defineProps({
    id: {
        type: String,
        required: true,
    },
    size: {
        type: String,
        default: () => 'base',
    },
    modelValue: {
        validator: (v) => Array.isArray(v),
        required: true,
    },
    label: {
        type: String,
        default: () => '',
    },
    options: {
        validator: (v) => Array.isArray(v),
        default: () => [],
    },
});

const visible = ref(false);
const parsedOptions = computed(() =>
    Array.isArray(props.options)
        ? props.options
        : map(props.options, (value, key) => {
              return {name: value, id: key};
          })
);
function trigger(option, v) {
    var value = JSON.parse(JSON.stringify(props.modelValue));

    emit('update:modelValue', value.includes(option.id) ? value.filter((cv) => cv !== option.id) : [...value, option.id]);
}
</script>
