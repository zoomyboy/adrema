<template>
    <label class="field-wrap" :for="id" :class="`field-wrap-${size}`">
        <span v-if="label" class="field-label">
            {{ label }}
            <span v-show="required" class="text-red-800">&nbsp;*</span>
        </span>
        <div class="real-field-wrap" :class="`size-${size}`">
            <select :disabled="disabled" :name="name" :value="value" @change="trigger">
                <option v-if="placeholder" v-html="placeholder" :value="null"></option>

                <option
                    v-for="option in parsedOptions"
                    :key="option.id"
                    v-html="option.name"
                    :value="option.id"
                ></option>
            </select>
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
            this.$emit(
                'input',
                isNaN(parseInt(v.target.value)) ? (v.target.value ? v.target.value : null) : parseInt(v.target.value)
            );
        },
        clear() {
            this.$emit('input', null);
        },
    },
    mounted() {
        if (this.def !== -1 && typeof this.value === 'undefined') {
            this.$emit('input', this.def);
            return;
        }

        if (this.placeholder && typeof this.value === 'undefined') {
            this.$emit('input', null);
        }
    },
};
</script>

<style scope>
.inset-bg {
    background: linear-gradient(
        to bottom,
        hsl(247.5, 66.7%, 97.6%) 0%,
        hsl(247.5, 66.7%, 97.6%) 41%,
        hsl(0deg 0% 100%) 41%,
        hsl(180deg 0% 100%) 100%
    );
}
</style>
