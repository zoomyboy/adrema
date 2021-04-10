<template>
    <label class="flex flex-col relative">
        <span v-if="label && !inset" class="font-semibold text-gray-700" :class="{
            'text-xs': size == 'sm',
            'text-sm': size === null
        }">{{ label }}<span v-show="required" class="text-red-800">&nbsp;*</span></span>
        <span v-if="label && inset" class="absolute top-0 left-0 -mt-2 px-1 ml-3 inset-bg font-semibold text-gray-700" :class="{
            'text-xs': size == 'sm',
            'text-sm': size === null
        }">{{ label }}<span v-show="required" class="text-red-800">&nbsp;*</span></span>
        <textarea v-text="value" @input="trigger" :placeholder="placeholder"
            class="h-full outline-none border-gray-400 border-solid" :rows="rows"
            :class="{
                'rounded-lg text-sm border-2 p-2 text-gray-800': size === null,
                'rounded-lg py-2 px-2 text-xs border-2 text-gray-800': size == 'sm'
            }"
        ></textarea>
        <div v-if="hint" v-tooltip="hint" class="absolute right-0 top-0 mr-2 mt-2">
            <sprite src="info-button" class="w-5 h-5 text-indigo-200"></sprite>
        </div>
    </label>
</template>

<script>
export default {
    data: function() {
        return {
            focus: false
        };
    },
    props: {
        required: {
            type: Boolean,
            default: false
        },
        inset: {
            default: false,
            type: Boolean
        },
        size: {
            default: null
        },
        rows: {
            default: function() {
                return 4;
            }
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
        placeholder: {
            default: ''
        }
    },
    methods: {
        trigger(v) {
            this.$emit('input', v.target.value);
        }
    },
    created() {
        if (typeof this.value === 'undefined') {
            this.$emit('input', '');
        }
    }
};
</script>

<style scope>
.inset-bg {
    background: linear-gradient(to bottom, hsl(247.5, 66.7%, 97.6%) 0%, hsl(247.5, 66.7%, 97.6%) 41%, hsl(0deg 0% 100%) 41%, hsl(180deg 0% 100%) 100%);
}
</style>
