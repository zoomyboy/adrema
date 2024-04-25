<template>
    <div>
        <div>
            <span v-if="label" class="font-semibold text-gray-400" :class="labelClass(size)">{{ label }}<span v-show="required" class="text-red-800">&nbsp;*</span></span>
            <div class="relative w-full h-full">
                <div :id="id" :class="[defaultFieldClass, fieldClass(size)]"></div>
                <div v-if="hint" v-tooltip="hint" class="absolute right-0 top-0 mr-2 mt-2">
                    <ui-sprite src="info-button" class="w-5 h-5 text-indigo-200"></ui-sprite>
                </div>
            </div>
        </div>

        <ui-popup
            v-if="condition !== null"
            heading="Bedingungen"
            @close="
                condition.resolve(condition.data);
                condition = null;
            "
        >
            <slot name="conditions" :data="condition.data" :resolve="condition.resolve" :reject="condition.reject"></slot>
        </ui-popup>
    </div>
</template>

<script setup>
import {debounce} from 'lodash';
import {onMounted, ref} from 'vue';
import EditorJS from '@editorjs/editorjs';
import Header from '@editorjs/header';
import Paragraph from '@editorjs/paragraph';
import NestedList from '@editorjs/nested-list';
import Alert from 'editorjs-alert';
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
    conditions: {
        required: false,
        type: Boolean,
        default: () => false,
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
const condition = ref(null);

async function openPopup(data) {
    return new Promise((resolve, reject) => {
        new Promise((innerResolve, innerReject) => {
            condition.value = {
                resolve: innerResolve,
                reject: innerReject,
                data: data,
            };
        }).then((data) => {
            resolve(data);
            condition.value = null;
        });
    });
}

class ConditionTune {
    constructor({api, data, config, block}) {
        this.api = api;
        this.data = data || {
            mode: 'all',
            ifs: [],
        };
        this.config = config;
        this.block = block;
        this.wrapper = null;
    }

    static get isTune() {
        return true;
    }

    wrap(blockContent) {
        this.wrapper = document.createElement('div');

        var tooltip = document.createElement('div');
        tooltip.setAttribute('data-tooltip', '');

        var content = document.createElement('div');
        content.setAttribute('data-content', '');

        content.appendChild(blockContent);

        this.wrapper.appendChild(tooltip);
        this.wrapper.appendChild(content);

        this.styleWrapper();

        return this.wrapper;
    }

    hasData() {
        return this.data.ifs.length > 0;
    }

    styleWrapper() {
        if (this.hasData()) {
            this.wrapper.querySelector('[data-content]').className = 'p-1 border border-blue-100 rounded';
            this.wrapper.querySelector('[data-tooltip]').className =
                'mt-1 inline-block tracking-wider font-semibold ml-2 mr-2 px-2 py-1 items-center text-xs leading-none bg-blue-100 text-blue-900 rounded-t-lg';
            this.wrapper.querySelector('[data-tooltip]').innerHTML = this.descriptionName();
        } else {
            this.wrapper.querySelector('[data-content]').className = '';
            this.wrapper.querySelector('[data-tooltip]').className = '';
            this.wrapper.querySelector('[data-tooltip]').innerHTML = '';
        }
    }

    descriptionName() {
        return (
            'Bedingung ' +
            this.data.ifs
                .map((i) => {
                    var parts = [i.field];

                    if (i.comparator === 'isEqual' || i.comparator === 'isIn') {
                        parts.push('=');
                    }

                    if (i.comparator === 'isNotEqual' || i.comparator === 'isNotIn') {
                        parts.push('&ne;');
                    }

                    if (typeof i.value === 'string') {
                        parts.push(i.value);
                    }

                    if (Array.isArray(i.value)) {
                        parts.push(i.value.join(', '));
                    }

                    if (typeof i.value === 'boolean') {
                        parts.push(i.value ? 'An' : 'Aus');
                    }

                    return parts.join(' ');
                })
                .join(', ')
        );
    }

    render() {
        return {
            label: 'Bedingungen',
            closeOnActivate: true,
            toggle: true,
            onActivate: async () => {
                this.data = await openPopup(this.data);
                this.styleWrapper();
                this.block.dispatchChange();
            },
        };
    }

    save() {
        return this.data;
    }
}

onMounted(async () => {
    var tools = {
        paragraph: {
            class: Paragraph,
            shortcut: 'CTRL+P',
            inlineToolbar: true,
            config: {
                preserveBlank: true,
                placeholder: 'Absatz',
            },
        },
        alert: {
            class: Alert,
            inlineToolbar: true,
            config: {
                defaultType: 'primary',
            },
        },
        heading: {
            class: Header,
            shortcut: 'CTRL+H',
            inlineToolbar: true,
            config: {
                placeholder: 'Überschrift',
                levels: [2, 3, 4],
                defaultLevel: 2,
            },
        },
        list: {
            class: NestedList,
            shortcut: 'CTRL+L',
            inlineToolbar: true,
        },
    };

    var tunes = [];

    if (props.conditions) {
        tools.condition = {
            class: ConditionTune,
        };
        tunes.push('condition');
    }

    editor.value = new EditorJS({
        placeholder: props.placeholder,
        holder: props.id,
        minHeight: 0,
        defaultBlock: 'paragraph',
        data: JSON.parse(JSON.stringify(props.modelValue)),
        tunes: tunes,
        tools: tools,
        onChange: debounce(async (api, event) => {
            const data = await editor.value.save();
            console.log(data);
            emit('update:modelValue', data);
        }, 200),
        onPopup: () => {
            console.log('opened');
        },
    });
    await editor.value.isReady;
    console.log('Editor is ready');
});
</script>
