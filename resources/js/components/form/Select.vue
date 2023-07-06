<template>
    <label class="field-wrap" :for="id" :class="`field-wrap-${size}`">
        <span v-if="label" class="field-label">
            {{ label }}
            <span v-show="required" class="text-red-800">&nbsp;*</span>
        </span>
        <div class="real-field-wrap" :class="`size-${size}`">
            <select :disabled="disabled" :name="name" :value="modelValue" @change="trigger">
                <option v-if="placeholder" value="">{{ placeholder }}</option>

                <option v-for="option in parsedOptions" :key="option.id" :value="option.id">{{ option.name }}</option>
            </select>
            <div class="info-wrap">
                <div v-if="hint" v-tooltip="hint">
                    <ui-sprite src="info-button" class="info-button"></ui-sprite>
                </div>
                <div class="px-1 relative" v-if="size != 'xs'">
                    <ui-sprite class="chevron w-3 h-3 fill-current" src="chevron-down"></ui-sprite>
                </div>
                <div class="px-1 relative" v-if="size == 'xs'">
                    <ui-sprite class="chevron w-2 h-2 fill-current" src="chevron-down"></ui-sprite>
                </div>
            </div>
        </div>
    </label>
</template>

<script>
import map from 'lodash/map';

export default {
    emits: ['update:modelValue'],
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
        placeholder: {
            default: '--kein--',
            type: String,
        },
        def: {
            required: false,
            type: Number,
            default: -1,
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
        trigger(v) {
            this.$emit('update:modelValue', /^[0-9]+$/.test(v.target.value) ? parseInt(v.target.value) : v.target.value ? v.target.value : null);
        },
        clear() {
            this.$emit('update:modelValue', null);
        },
    },
    mounted() {
        if (this.def !== -1 && typeof this.modelValue === 'undefined') {
            this.$emit('update:modelValue', this.def);
            return;
        }

        if (this.placeholder && typeof this.modelValue === 'undefined') {
            this.$emit('update:modelValue', null);
        }
    },
};
</script>

<style scope>
.inset-bg {
    background: linear-gradient(to bottom, hsl(247.5, 66.7%, 97.6%) 0%, hsl(247.5, 66.7%, 97.6%) 41%, hsl(0deg 0% 100%) 41%, hsl(180deg 0% 100%) 100%);
}
</style>
