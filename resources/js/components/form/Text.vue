<template>
    <label class="flex flex-col group" :for="id" :class="sizes[size]">
        <span v-if="label" class="font-semibold leading-none text-gray-400 group-[.field-base]:text-sm group-[.field-sm]:text-xs">
            {{ label }}
            <span v-show="required" class="text-red-800">&nbsp;*</span>
        </span>
        <div class="relative flex-none flex">
            <input
                :name="name"
                :type="type"
                :value="transformedValue"
                :disabled="disabled"
                placeholder=""
                :min="min"
                :max="max"
                class="group-[.field-base]:h-[35px] group-[.field-sm]:h-[23px] group-[.field-base]:border-2 group-[.field-sm]:border border-gray-600 border-solid text-gray-300 bg-gray-700 leading-none rounded-lg group-[.field-base]:text-sm group-[.field-sm]:text-xs py-0 group-[.field-base]:px-2 group-[.field-sm]:px-1 w-full"
                @keypress="$emit('keypress', $event)"
                @input="onInput"
                @change="onChange"
                @focus="onFocus"
                @blur="onBlur"
            />
            <div v-if="hint" class="h-full items-center flex absolute top-0 right-0">
                <div v-tooltip="hint">
                    <ui-sprite src="info-button" class="text-primary-700"></ui-sprite>
                </div>
            </div>
        </div>
    </label>
</template>

<script>
import wNumb from 'wnumb';

var numb = {
    natural: wNumb({
        mark: '',
        thousand: '',
        decimals: 0,
        decoder: (a) => a * 100,
        encoder: (a) => a / 100,
    }),
    area: wNumb({
        mark: ',',
        thousand: '',
        decimals: 2,
        decoder: (a) => a * 100,
        encoder: (a) => a / 100,
    }),
};

var transformers = {
    none: {
        display: {
            to: (v) => v,
            from: (v) => v,
        },
        edit: {
            to: (v) => v,
            from: (v) => v,
        },
    },
    area: {
        display: {
            to: (v) => (v === null ? '' : numb.area.to(v)),
            from: (v) => (v === '' ? null : numb.area.from(v)),
        },
        edit: {
            to(v) {
                if (v === null) {
                    return '';
                }
                if (Math.round(v / 100) * 100 === v) {
                    return numb.natural.to(v);
                }
                return numb.area.to(v);
            },
            from(v) {
                if (v === '') {
                    return null;
                }
                if (v.indexOf(',') === -1) {
                    return numb.natural.from(v);
                }

                return numb.area.from(v);
            },
        },
    },
};

export default {
    props: {
        default: {},
        mode: {
            default: function () {
                return 'none';
            },
        },
        required: {
            type: Boolean,
            default: false,
        },
        inset: {
            default: function () {
                return null;
            },
        },
        size: {
            default: function () {
                return 'base';
            },
        },
        id: {
            required: true,
        },
        hint: {
            default: null,
        },
        modelValue: {},
        mask: {
            default: undefined,
        },
        label: {
            default: false,
        },
        type: {
            required: false,
            default: function () {
                return 'text';
            },
        },
        disabled: {
            default: false,
            type: Boolean,
        },
        min: {
            default: () => '',
        },
        max: {
            default: () => '',
        },
        name: {},
    },
    data: function () {
        return {
            focus: false,
            sizes: {
                sm: 'field-sm',
                base: 'field-base',
                lg: 'field-lg',
            },
        };
    },
    computed: {
        transformedValue: {
            get() {
                return transformers[this.mode][this.focus ? 'edit' : 'display'].to(this.modelValue);
            },
            set(v) {
                this.$emit('update:modelValue', transformers[this.mode][this.focus ? 'edit' : 'display'].from(v));
            },
        },
        insetClass() {
            if (this.inset === '') {
                return 'bg-inset';
            }
            if (this.inset === undefined) {
                return null;
            }

            return `bg-${this.inset}`;
        },
    },
    created() {
        if (typeof this.modelValue === 'undefined') {
            this.$emit('update:modelValue', this.default === undefined ? '' : this.default);
        }
    },
    methods: {
        onFocus() {
            this.focus = true;
        },
        onBlur() {
            this.focus = false;
        },
        onChange(v) {
            if (this.mode !== 'none') {
                this.transformedValue = v.target.value;
            }
        },
        onInput(v) {
            if (this.mode === 'none') {
                this.transformedValue = v.target.value;
            }
        },
    },
};
</script>

<style scope>
.bg-inset {
    background: linear-gradient(to bottom, hsl(247.5, 66.7%, 97.6%) 0%, hsl(247.5, 66.7%, 97.6%) 41%, hsl(0deg 0% 100%) 41%, hsl(180deg 0% 100%) 100%);
}
</style>
