<template>
    <label
        class="flex relative field-switch cursor-pointer"
        :for="id"
        :class="{
            'items-center flex-row-reverse space-x-3 space-x-reverse justify-end': inline,
            'flex-col': !inline,
            [sizes[size].wrap]: true,
        }"
    >
        <span
            v-if="label"
            class="font-semibold leading-none text-gray-400"
            :class="{
                'text-xs': size == 'sm',
                'text-sm': size === 'base',
            }"
            >{{ label }}</span
        >
        <div class="relative flex items-center inner-field mt-1" :class="`h-field-${fieldSize}`">
            <span>
                <input :id="id" type="checkbox" :name="name" :value="value" v-model="v" :disabled="disabled" class="absolute peer" @keypress="$emit('keypress', $event)" />
                <span class="relative cursor-pointer peer-focus:bg-red-500 flex grow display" :class="{'bg-switch': v === true, 'bg-gray-700': v === false}">
                    <span><svg-sprite class="relative text-gray-400 flex-none" :class="{'w-2 h-2': size === 'sm' || size == 'xs', 'w-4 h-4': size === 'base'}" src="check"></svg-sprite></span>
                    <span><svg-sprite class="relative text-gray-400 flex-none" :class="{'w-2 h-2': size === 'sm' || size == 'xs', 'w-4 h-4': size === 'base'}" src="close"></svg-sprite></span>
                    <var class="absolute overlay bg-gray-400 rounded top-0"></var>
                </span>
            </span>
            <div v-if="hint" class="ml-2 info-wrap">
                <div v-tooltip="hint">
                    <svg-sprite src="info-button" class="info-button w-4 h-4 text-primary-700"></svg-sprite>
                </div>
            </div>
        </div>
    </label>
</template>

<script>
export default {
    data: function () {
        return {
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
    model: {
        prop: 'items',
        event: 'input',
    },
    props: {
        hint: {
            default: null,
        },
        inline: {
            default: false,
            type: Boolean,
        },
        size: {
            default: 'base',
            required: false,
        },
        id: {
            required: true,
        },
        name: {
            default: '',
        },
        disabled: {
            type: Boolean,
            default: false,
        },
        value: {
            default: false,
        },
        label: {
            default: false,
        },
        items: {
            default: undefined,
        },
    },
    computed: {
        v: {
            set(v) {
                if (this.disabled === true) {
                    return;
                }

                if (typeof this.items === 'boolean') {
                    this.$emit('input', v);
                    return;
                }

                var a = this.items.filter((i) => i !== this.value);
                if (v) {
                    a.push(this.value);
                }

                this.$emit('input', a);
            },
            get() {
                if (typeof this.items === 'boolean') {
                    return this.items;
                }

                if (typeof this.items === 'undefined') {
                    return this.$emit('input', false);
                }

                return this.items.indexOf(this.value) !== -1;
            },
        },
        fieldSize() {
            var sizes = ['xxs', 'xs', 'sm', 'md', 'lg'];

            var sizeIndex = sizes.findIndex((s) => s === this.size);
            return sizes[sizeIndex - 1];
        },
    },
};
</script>
