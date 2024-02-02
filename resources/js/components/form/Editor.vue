<template>
    <div>
        <span v-if="label" class="font-semibold text-gray-400" :class="labelClass(size)">{{ label }}<span v-show="required" class="text-red-800">&nbsp;*</span></span>
        <div class="relative w-full h-full">
            <div :id="id" :class="[defaultFieldClass, fieldClass(size)]"></div>
            <div v-if="hint" v-tooltip="hint" class="absolute right-0 top-0 mr-2 mt-2">
                <ui-sprite src="info-button" class="w-5 h-5 text-indigo-200"></ui-sprite>
            </div>
        </div>
    </div>
</template>

<script setup>
import {debounce} from 'lodash';
import {onMounted, ref} from 'vue';
import EditorJS from '@editorjs/editorjs';
import Header from '@editorjs/header';
import Paragraph from '@editorjs/paragraph';
import useFieldSize from '../../composables/useFieldSize.js';
const emit = defineEmits(['update:modelValue']);

const {labelClass, fieldClass, defaultFieldClass} = useFieldSize();

const props = defineProps({
    required: {
        type: Boolean,
        default: false,
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
    label: {
        default: false,
    },
    placeholder: {
        default: '',
    },
});

const editor = ref(null);

onMounted(async () => {
    editor.value = new EditorJS({
        placeholder: props.placeholder,
        holder: props.id,
        defaultBlock: 'paragraph',
        data: JSON.parse(JSON.stringify(props.modelValue)),
        tools: {
            paragraph: {
                class: Paragraph,
                shortcut: 'SHIFT+P',
                inlineToolbar: true,
                config: {
                    preserveBlank: true,
                    placeholder: 'Absatz',
                },
            },
            heading: {
                class: Header,
                shortcut: 'CTRL+H',
                inlineToolbar: [],
                config: {
                    placeholder: 'Überschrift',
                    levels: [2, 3, 4],
                    defaultLevel: 2,
                },
            },
        },
        onChange: debounce(async (api, event) => {
            const data = await editor.value.save();
            emit('update:modelValue', data);
        }, 500),
    });
    await editor.value.isReady;
    console.log('Editor is ready');
});
</script>
