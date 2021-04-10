<template>
    <label class="flex flex-col relative" :for="id" :class="{['h-field-'+size]: inset === true}">
        <div class="relative h-full flex flex-col">
            <span v-if="label && !inset" class="font-semibold relative z-10 text-gray-700" :class="{
                'text-xs': size == 'sm',
                'text-sm': size === null
            }">{{ label }}<span v-show="required" class="text-red-800">&nbsp;*</span></span>
            <span v-if="label && inset" class="absolute z-10 top-0 left-0 -mt-2 px-1 ml-3 inset-bg font-semibold text-gray-700" :class="{
                'text-xs': size == 'sm',
                'text-sm': size === null
            }" v-text="label"></span>
            <div class="relative h-full" :class="{['h-field-'+size]: inset === false}">
                <input :type="type" :name="name" :value="transformedValue" @input="onInput" @change="onChange" :disabled="disabled" :placeholder="placeholder"
                    @focus="onFocus" @blur="onBlur"
                    class="border-gray-400 border-solid bg-white w-full appearance-none outline-none h-full"
                    :class="{
                        'rounded-lg text-sm border-2 p-2 text-gray-800': size === null,
                        'rounded-lg py-2 px-2 text-xs border-2 text-gray-800': size == 'sm'
                    }"
                />
                <div class="absolute top-0 right-0 -mx-1 flex items-center h-full cursor-pointer">
                    <div v-if="hint" class="absolute top-0 right-0 h-full items-center mr-2 flex w-6" v-tooltip="hint">
                        <sprite src="info-button" class="w-5 h-5 text-indigo-200"></sprite>
                    </div>
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
        }
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
        }
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
        }
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
        }
    })
};

var transformers = {
    none: {
        display: {
            to(v) { return v; },
            from(v) { return v; }
        },
        edit: {
            to(v) { return v; },
            from(v) { return v; }
        }
    },
    natural: {
        display: {
            to(v) { return isNaN(parseInt(v)) ? '' : numb.natural.to(v); },
            from(v) { return v === '' ? null : numb.natural.from(v); }
        },
        edit: {
            to(v) { return isNaN(parseInt(v)) ? '' : numb.naturalRaw.to(v); },
            from(v) { return v === '' ? null : numb.naturalRaw.from(v); }
        }
    },
    area: {
        display: {
            to(v) { return v === null ? '' : numb.area.to(v); },
            from(v) { return v === '' ? null : numb.area.from(v); }
        },
        edit: {
            to(v) {
                if (v === null) { return ''; }
                if (Math.round(v / 100) * 100 === v) { return numb.naturalRaw.to(v); }
                return numb.twoDecimalRaw.to(v);
            },
            from(v) {
                if (v === '') { return null; }
                if (v.indexOf(',') === -1) { return numb.naturalRaw.from(v); }

                return numb.twoDecimalRaw.from(v);
            }
        }
    },
    currency: {
        display: {
            to(v) { return v === null ? '' : numb.area.to(v); },
            from(v) { return v === '' ? null : numb.area.from(v); }
        },
        edit: {
            to(v) {
                if (v === null) { return ''; }
                if (Math.round(v / 100) * 100 === v) { return numb.naturalRaw.to(v); }
                return numb.twoDecimalRaw.to(v);
            },
            from(v) {
                if (v === '') { return null; }
                if (v.indexOf(',') === -1) { return numb.naturalRaw.from(v); }

                return numb.twoDecimalRaw.from(v);
            }
        }
    }
};

export default {
    data: function() {
        return {
            focus: false
        };
    },
    props: {
        name: {
            default: function() {
                return '';
            }
        },
        placeholder: {
            default: function() {
                return '';
            }
        },
        default: {},
        mode: {
            default: function() { return 'none'; }
        },
        required: {
            type: Boolean,
            default: false
        },
        inset: {
            default: function() {
                return null;
            }
        },
        size: {
            default: null
        },
        id: {
            required: true
        },
        hint: {
            default: null
        },
        value: {
            default: undefined
        },
        mask: {
            default: undefined
        },
        label: {
            default: false
        },
        type: {
            required: false,
            default: function() { return 'text'; }
        },
        disabled: {
            default: false,
            type: Boolean
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
        }
    },
    computed: {
        transformedValue: {
            get() {
                return transformers[this.mode][this.focus ? 'edit' : 'display'].to(this.value);
            },
            set(v) {
                this.$emit('input', transformers[this.mode][this.focus ? 'edit' : 'display'].from(v));
            }
        },
        insetClass() {
            if (this.inset === '') { return 'bg-inset'; }
            if (this.inset === undefined) { return null; }

            return `bg-${this.inset}`;
        }
    },
    created() {
        if (typeof this.value === 'undefined') {
            this.$emit('input', this.default === undefined ? '' : this.default);
        }
    }
};
</script>

<style scope>
.bg-inset {
    background: linear-gradient(to bottom, hsl(247.5, 66.7%, 97.6%) 0%, hsl(247.5, 66.7%, 97.6%) 41%, hsl(0deg 0% 100%) 41%, hsl(180deg 0% 100%) 100%);
}
</style>
