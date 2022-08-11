<template>
    <label class="flex flex-col relative field-switch cursor-pointer" :for="id" :class="{[`size-${outerSize}`]: true}">
        <span
            v-if="label"
            class="font-semibold leading-none text-gray-400"
            :class="{
                'text-xs': size == 'sm',
                'text-sm': size === null,
            }"
            >{{ label }}</span
        >
        <div class="relative inner-field mt-1" :class="`h-field-${fieldSize}`">
            <input
                :id="id"
                type="checkbox"
                :name="name"
                :value="value"
                v-model="v"
                :disabled="disabled"
                class="absolute peer"
                @keypress="$emit('keypress', $event)"
            />
            <span
                class="relative cursor-pointer peer-focus:bg-red-500 flex grow display"
                :class="{'bg-switch': v === true, 'bg-gray-700': v === false}"
            >
                <span
                    ><svg-sprite
                        class="relative text-gray-400 flex-none"
                        :class="{'w-2 h-2': size === 'sm' || size == 'xs', 'w-4 h-4': size === null}"
                        src="check"
                    ></svg-sprite
                ></span>
                <span
                    ><svg-sprite
                        class="relative text-gray-400 flex-none"
                        :class="{'w-2 h-2': size === 'sm' || size == 'xs', 'w-4 h-4': size === null}"
                        src="close"
                    ></svg-sprite
                ></span>
                <var class="absolute overlay bg-gray-400 rounded top-0"></var>
            </span>
        </div>
    </label>
</template>

<script>
export default {
    model: {
        prop: 'items',
        event: 'input',
    },
    props: {
        inset: {
            type: Boolean,
            default: false,
        },
        size: {
            default: null,
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
            return sizes[this.inset ? sizeIndex : sizeIndex - 1];
        },
        outerSize() {
            var sizes = ['xxs', 'xs', 'sm', 'md', 'lg'];

            var sizeIndex = sizes.findIndex((s) => s === this.size);
            if (!this.label || this.inset) {
                sizeIndex--;
            }
            return sizes[sizeIndex];
        },
    },
};
</script>
