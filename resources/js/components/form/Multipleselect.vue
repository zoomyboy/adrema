<template>
    <label class="field-wrap" :for="id" :class="`field-wrap-${size}`">
        <span v-if="label" class="field-label">
            {{ label }}
            <span v-show="required" class="text-red-800">&nbsp;*</span>
        </span>
        <div class="relative real-field-wrap" :class="`size-${size}`">
            <div
                @click="visible = !visible"
                class="flex items-center border-gray-600 text-gray-300 leading-none border-solid bg-gray-700 w-full appearance-none outline-none rounded-lg size-sm text-xs px-1 border pr-6"
                v-text="`${value.length} Einträge ausgewählt`"
            ></div>
            <div v-show="visible" class="absolute h-[31rem] overflow-auto shadow-lg bg-gray-600 border border-gray-500 rounded-lg p-2 top-7">
                <div v-for="(option, index) in parsedOptions" class="flex items-center space-x-2" :key="index">
                    <f-switch :id="`${id}-${index}`" size="sm" :items="value.includes(option.id)" :value="option.id" @input="trigger(option, $event)"></f-switch>
                    <div class="text-sm text-gray-200" v-text="option.name"></div>
                </div>
            </div>

            <div class="info-wrap">
                <div v-if="hint" v-tooltip="hint">
                    <svg-sprite src="info-button" class="info-button"></svg-sprite>
                </div>
                <div class="px-1 relative" v-if="size != 'xs'">
                    <svg-sprite class="chevron w-3 h-3 fill-current" src="chevron-down"></svg-sprite>
                </div>
                <div class="px-1 relative" v-if="size == 'xs'">
                    <svg-sprite class="chevron w-2 h-2 fill-current" src="chevron-down"></svg-sprite>
                </div>
            </div>
        </div>
    </label>
</template>

<script>
import map from 'lodash/map';

export default {
    data: function () {
        return {
            visible: false,
        };
    },
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
        value: {
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
    computed: {
        parsedOptions() {
            return Array.isArray(this.options)
                ? this.options
                : map(this.options, (value, key) => {
                      return {name: value, id: key};
                  });
        },
    },
    methods: {
        trigger(option, v) {
            var value = [...this.value];

            this.$emit('input', value.includes(option.id) ? value.filter((cv) => cv !== option.id) : [...value, option.id]);
        },
        clear() {
            this.$emit('input', null);
        },
    },
};
</script>

<style scope>
.inset-bg {
    background: linear-gradient(to bottom, hsl(247.5, 66.7%, 97.6%) 0%, hsl(247.5, 66.7%, 97.6%) 41%, hsl(0deg 0% 100%) 41%, hsl(180deg 0% 100%) 100%);
}
</style>
