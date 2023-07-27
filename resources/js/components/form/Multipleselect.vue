<template>
    <label class="field-wrap" :for="id" :class="`field-wrap-${size}`">
        <span v-if="label" class="field-label">
            {{ label }}
            <span v-show="required" class="text-red-800">&nbsp;*</span>
        </span>
        <div class="relative real-field-wrap" :class="`size-${size}`">
            <div class="flex items-center border-gray-600 text-gray-300 leading-none border-solid bg-gray-700 w-full appearance-none outline-none rounded-lg size-sm text-xs px-1 border pr-6"
                @click="visible = !visible" v-text="`${modelValue.length} Einträge ausgewählt`"></div>
            <div v-show="visible"
                class="absolute w-[max-content] z-30 max-h-[31rem] overflow-auto shadow-lg bg-gray-600 border border-gray-500 rounded-lg p-2 top-7">
                <div v-for="(option, index) in parsedOptions" :key="index" class="flex items-center space-x-2">
                    <f-switch :id="`${id}-${index}`" size="sm" :model-value="modelValue.includes(option.id)"
                        :value="option.id" @update:modelValue="trigger(option, $event)"></f-switch>
                    <div class="text-sm text-gray-200" v-text="option.name"></div>
                </div>
            </div>

            <div class="info-wrap">
                <div v-if="hint" v-tooltip="hint">
                    <ui-sprite src="info-button" class="info-button"></ui-sprite>
                </div>
                <div v-if="size != 'xs'" class="px-1 relative">
                    <ui-sprite class="chevron w-3 h-3 fill-current" src="chevron"></ui-sprite>
                </div>
                <div v-if="size == 'xs'" class="px-1 relative">
                    <ui-sprite class="chevron w-2 h-2 fill-current" src="chevron"></ui-sprite>
                </div>
            </div>
        </div>
    </label>
</template>

<script>
import map from 'lodash/map';

export default {
    props: {
        disabled: {
            type: Boolean,
            default: function () {
                return false;
            },
        },
        id: {},
        inset: {
            type: Boolean,
            default: false,
        },
        size: {
            default: function () {
                return 'base';
            },
        },
        emptyLabel: {
            default: false,
            type: Boolean,
        },
        modelValue: {
            default: undefined,
        },
        label: {
            default: null,
        },
        required: {
            type: Boolean,
            default: false,
        },
        name: {
            required: true,
        },
        hint: {},
        options: {
            default: function () {
                return [];
            },
        },
    },
    emits: ['update:modelValue'],
    data: function () {
        return {
            visible: false,
        };
    },
    computed: {
        parsedOptions() {
            return Array.isArray(this.options)
                ? this.options
                : map(this.options, (value, key) => {
                    return { name: value, id: key };
                });
        },
    },
    methods: {
        trigger(option, v) {
            var value = [...this.modelValue];

            this.$emit('update:modelValue', value.includes(option.id) ? value.filter((cv) => cv !== option.id) : [...value, option.id]);
        },
        clear() {
            this.$emit('update:modelValue', null);
        },
    },
};
</script>

<style scope>
.inset-bg {
    background: linear-gradient(to bottom, hsl(247.5, 66.7%, 97.6%) 0%, hsl(247.5, 66.7%, 97.6%) 41%, hsl(0deg 0% 100%) 41%, hsl(180deg 0% 100%) 100%);
}
</style>
