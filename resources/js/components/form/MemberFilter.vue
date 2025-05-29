<template>
    <label class="flex flex-col group" :for="id" :class="sizeClass(size)">
        <f-label v-if="label" :required="false" :value="label"></f-label>
        <div class="relative flex-none flex">
            <ui-icon-button :class="[fieldHeight, fieldAppearance, paddingX]" icon="filter" @click="visible = true">Filtern</ui-icon-button>
            <f-hint v-if="hint" :value="hint"></f-hint>
        </div>
    </label>

    <ui-filter-sidebar v-model="visible">
        <member-filter-fields :model-value="modelValue" @update:model-value="$emit('update:modelValue', $event)" />
    </ui-filter-sidebar>
</template>

<script setup>
import {ref} from 'vue';
import useFieldSize from '../../composables/useFieldSize';
import MemberFilterFields from '../../views/member/MemberFilterFields.vue';

const {sizeClass, fieldHeight, fieldAppearance, paddingX} = useFieldSize();

const visible = ref(false);

defineEmits(['update:modelValue']);

const props = defineProps({
    modelValue: {
        type: Object,
        required: true,
    },
    label: {
        type: String,
        required: true,
    },
    id: {
        type: String,
        required: true,
    },
    size: {
        type: String,
        default: () => 'base',
    },
    hint: {
        type: String,
        default: () => '',
    },
});
</script>
