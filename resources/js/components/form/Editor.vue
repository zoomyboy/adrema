<template>
    <div>
        <div class="flex flex-col group" :for="id" :class="sizeClass(size)">
            <f-label v-if="label" :required="required" :value="label" />
            <div class="relative w-full h-full">
                <div :id="id" :class="[fieldAppearance, paddingX, paddingY]" />
                <f-hint v-if="hint" :value="hint" />
            </div>
        </div>

        <ui-popup v-if="condition !== null"
                  heading="Bedingungen"
                  @close="
                      condition.resolve(condition.data);
                      condition = null;
                  "
        >
            <slot name="conditions" :data="condition.data" :resolve="condition.resolve" :reject="condition.reject" />
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
    conditions: {
        required: false,
        type: Boolean,
        default: () => false,
    },
    hint: {
        type: String,
        default: () => '',
    },
    modelValue: {
        default: undefined,
    },
    label: {
        type: String,
        default: () => '',
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

        const tooltip = document.createElement('div');
        tooltip.setAttribute('data-tooltip', '');

        const content = document.createElement('div');
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
                    const parts = [i.field];

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
    const tools = {
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

    const tunes = [];

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
