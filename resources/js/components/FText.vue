<template>
    <label class="field-wrap field-wrap-sm" :for="id" :class="sizes[size].wrap">
        <span v-if="label" class="field-label">
            {{ label }}
            <span v-show="required" class="text-red-800">&nbsp;*</span>
        </span>
        <div class="real-field-wrap size-sm" :class="sizes[size].field">
            <input
                @keypress="$emit('keypress', $event)"
                :name="name"
                :type="type"
                :value="transformedValue"
                @input="onInput"
                @change="onChange"
                :disabled="disabled"
                :placeholder="placeholder"
                @focus="onFocus"
                @blur="onBlur"
            />
            <div v-if="hint" class="info-wrap">
                <div v-tooltip="hint">
                    <svg-sprite src="info-button" class="info-button"></svg-sprite>
                </div>
            </div>
        </div>
    </label>
</template>

<script>
import wNumb from 'wnumb';

var numb = {
    natural: wNumb({
        mark: ',',
        thousand: '.',
        decimals: 0,
        decoder(a) {
            return a * 100;
        },
        encoder(a) {
            return a / 100;
        },
    }),
    naturalRaw: wNumb({
        mark: '',
        thousand: '',
        decimals: 0,
        decoder(a) {
            return a * 100;
        },
        encoder(a) {
            return a / 100;
        },
    }),
    naturalDetailRaw: wNumb({
        mark: '',
        thousand: '',
        decimals: 0,
        decoder(a) {
            return a * 10000;
        },
        encoder(a) {
            return a / 10000;
        },
    }),
    area: wNumb({
        mark: ',',
        thousand: '.',
        decimals: 2,
        decoder(a) {
            return a * 100;
        },
        encoder(a) {
            return a / 100;
        },
    }),
    areaDetail: wNumb({
        mark: ',',
        thousand: '.',
        decimals: 4,
        decoder(a) {
            return a * 10000;
        },
        encoder(a) {
            return a / 10000;
        },
    }),
    twoDecimalRaw: wNumb({
        mark: ',',
        thousand: '',
        decimals: 2,
        decoder(a) {
            return a * 100;
        },
        encoder(a) {
            return a / 100;
        },
    }),
    fourDecimalRaw: wNumb({
        mark: ',',
        thousand: '',
        decimals: 4,
        decoder(a) {
            return a * 10000;
        },
        encoder(a) {
            return a / 10000;
        },
    }),
};

var transformers = {
    none: {
        display: {
            to(v) {
                return v;
            },
            from(v) {
                return v;
            },
        },
        edit: {
            to(v) {
                return v;
            },
            from(v) {
                return v;
            },
        },
    },
    natural: {
        display: {
            to(v) {
                return isNaN(parseInt(v)) ? '' : numb.natural.to(v);
            },
            from(v) {
                return v === '' ? null : numb.natural.from(v);
            },
        },
        edit: {
            to(v) {
                return isNaN(parseInt(v)) ? '' : numb.naturalRaw.to(v);
            },
            from(v) {
                return v === '' ? null : numb.naturalRaw.from(v);
            },
        },
    },
    area: {
        display: {
            to(v) {
                return v === null ? '' : numb.area.to(v);
            },
            from(v) {
                return v === '' ? null : numb.area.from(v);
            },
        },
        edit: {
            to(v) {
                if (v === null) {
                    return '';
                }
                if (Math.round(v / 100) * 100 === v) {
                    return numb.naturalRaw.to(v);
                }
                return numb.twoDecimalRaw.to(v);
            },
            from(v) {
                if (v === '') {
                    return null;
                }
                if (v.indexOf(',') === -1) {
                    return numb.naturalRaw.from(v);
                }

                return numb.twoDecimalRaw.from(v);
            },
        },
    },
    currency: {
        display: {
            to(v) {
                return v === null ? '' : numb.area.to(v);
            },
            from(v) {
                return v === '' ? null : numb.area.from(v);
            },
        },
        edit: {
            to(v) {
                if (v === null) {
                    return '';
                }
                if (Math.round(v / 100) * 100 === v) {
                    return numb.naturalRaw.to(v);
                }
                return numb.twoDecimalRaw.to(v);
            },
            from(v) {
                if (v === '') {
                    return null;
                }
                if (v.indexOf(',') === -1) {
                    return numb.naturalRaw.from(v);
                }

                return numb.twoDecimalRaw.from(v);
            },
        },
    },
    currencyDetail: {
        display: {
            to(v) {
                return v === null ? '' : numb.areaDetail.to(v);
            },
            from(v) {
                return v === '' ? null : numb.areaDetail.from(v);
            },
        },
        edit: {
            to(v) {
                if (v === null) {
                    return '';
                }
                if (Math.round(v / 10000) * 10000 === v) {
                    return numb.naturalDetailRaw.to(v);
                }
                return numb.fourDecimalRaw.to(v);
            },
            from(v) {
                if (v === '') {
                    return null;
                }
                if (v.indexOf(',') === -1) {
                    return numb.naturalDetailRaw.from(v);
                }

                return numb.fourDecimalRaw.from(v);
            },
        },
    },
};

export default {
    data: function () {
        return {
            focus: false,
            sizes: {
                sm: {
                    wrap: 'field-wrap-sm',
                    field: 'size-sm',
                },
                base: {
                    wrap: 'field-wrap-base',
                    field: 'size-base',
                },
                lg: {
                    wrap: 'field-wrap-lg',
                    field: 'size-lg',
                },
            },
        };
    },
    props: {
        placeholder: {
            default: function () {
                return '';
            },
        },
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
        value: {
            default: undefined,
        },
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
        name: {},
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
    computed: {
        transformedValue: {
            get() {
                return transformers[this.mode][this.focus ? 'edit' : 'display'].to(this.value);
            },
            set(v) {
                this.$emit('input', transformers[this.mode][this.focus ? 'edit' : 'display'].from(v));
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
        if (typeof this.value === 'undefined') {
            this.$emit('input', this.default === undefined ? '' : this.default);
        }
    },
};
</script>

<style scope>
.bg-inset {
    background: linear-gradient(
        to bottom,
        hsl(247.5, 66.7%, 97.6%) 0%,
        hsl(247.5, 66.7%, 97.6%) 41%,
        hsl(0deg 0% 100%) 41%,
        hsl(180deg 0% 100%) 100%
    );
}
</style>
