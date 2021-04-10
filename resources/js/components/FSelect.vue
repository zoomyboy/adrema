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
                <select :value="value" @change="trigger"
                    class="border-gray-400 border-solid bg-white w-full appearance-none outline-none h-full"
                    :class="{
                        'rounded-lg text-sm border-2 p-2 text-gray-800': size === null,
                        'rounded-lg py-2 px-2 text-xs border-2 text-gray-800': size == 'sm'
                    }"
                >
                    <option v-if="placeholder" v-html="placeholder" :value="null"></option>

                    <option v-for="(option, key) in parsedOptions" :key="key"
                        v-html="option" :value="key"
                    ></option>
                </select>

                <div class="absolute pointer-events-none top-0 right-0 -mx-1 flex items-center h-full mr-4 cursor-pointer">
                    <div v-if="hint" v-tooltip="hint" class="px-1">
                        <sprite src="info-button" class="w-5 h-5 text-indigo-200"></sprite>
                    </div>
                    <div class="px-1 relative">
                        <sprite class="w-3 h-3 fill-current" src="chevron-down"></sprite>
                    </div>
                </div>
            </div>
        </div>
    </label>
</template>

<script>
export default {
    props: {
        id: {},
        inset: {
            type: Boolean,
            default: false
        },
        size: {
            default: function() { return null; }
        },
        emptyLabel: {
            default: false,
            type: Boolean
        },
        value: {
            default: undefined
        },
        label: {
            default: null
        },
        required: {
            type: Boolean,
            default: false
        },
        placeholder: {
            default: '--kein--',
            type: String
        },
        def: {
            required: false,
            type: Number,
            default: -1
        },
        hint: {},
        options: {
            default: function() { return []; }
        }
    },
    computed: {
        parsedOptions() {
            return this.options;
        }
    },
    methods: {
        trigger(v) {
            this.$emit('input', isNaN(parseInt(v.target.value))
                ?  (v.target.value ? v.target.value : null)
                : parseInt(v.target.value)
            );
        },
        clear() {
            this.$emit('input', null);
        }
    },
    mounted() {
        if (this.def !== -1 && typeof this.value === 'undefined') {
            this.$emit('input', this.def);
            return;
        }

        if (this.placeholder && typeof this.value === 'undefined') {
            this.$emit('input', null);
        }
    }
};
</script>

<style scope>
.inset-bg {
    background: linear-gradient(to bottom, hsl(247.5, 66.7%, 97.6%) 0%, hsl(247.5, 66.7%, 97.6%) 41%, hsl(0deg 0% 100%) 41%, hsl(180deg 0% 100%) 100%);
}
</style>
