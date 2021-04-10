<template>
    <label class="flex flex-col relative field-switch cursor-pointer" :for="id" :class="{[`size-${outerSize}`]: true}">
        <span v-if="label && !inset" class="font-semibold leading-none text-gray-700" :class="{
            'text-xs': size == 'sm',
            'text-sm': size === null
        }">{{ label }}</span>
        <span v-if="label && inset" class="z-10 absolute top-0 left-0 -mt-2 px-1 ml-3 inset-bg font-semibold text-gray-700" :class="{
            'text-xs': size == 'sm',
            'text-sm': size === null
        }">{{ label }}</span>
        <div class="relative inner-field" :class="`h-field-${fieldSize}`">
            <input :id="id" type="checkbox" v-model="v" :disabled="disabled" class="invisible absolute" />
            <span class="relative cursor-pointer flex flex-grow display" :class="{'bg-primary': v === true, 'bg-gray-400': v === false}">
                <span><sprite class="relative text-white flex-none" :class="{'w-2 h-2': size === 'sm' || size == 'xs', 'w-6 h-6': size === null}" src="check"></sprite></span>
                <span><sprite class="relative text-white flex-none" :class="{'w-2 h-2': size === 'sm' || size == 'xs', 'w-6 h-6': size === null}" src="close"></sprite></span>
                <var class="absolute overlay bg-white rounded top-0"></var>
            </span>
        </div>
    </label>
</template>

<script>
export default {
    model: {
        prop: 'items',
        event: 'input'
    },
    props: {
        inset: {
            type: Boolean,
            default: false
        },
        size: {
            default: null,
            required: false
        },
        id: {
            required: true
        },
        disabled: {
            type: Boolean,
            default: false
        },
        value: {
            default: false
        },
        label: {
            default: false
        },
        items: {
            default: undefined
        },
        size: {
            default: null,
            type: String
        }
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

                var a = this.items.filter(i => i !== this.value);
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
            }
        },
        fieldSize() {
            var sizes = ['xxs', 'xs', 'sm', 'md', 'lg'];

            var sizeIndex = sizes.findIndex(s => s === this.size);
            return sizes[this.inset ? sizeIndex : sizeIndex - 1];
        },
        outerSize() {
            var sizes = ['xxs', 'xs', 'sm', 'md', 'lg'];

            var sizeIndex = sizes.findIndex(s => s === this.size);
            if (!this.label || this.inset) { sizeIndex--; }
            return sizes[sizeIndex];
        }
    }
};
</script>

<style lang="css">
:root {
    --margin: 0.2rem;
    --n-width: 2.5rem;
    --sm-width: 35px;
    --sm-margin: 0.2rem;
    --xs-width: 23px;
    --xs-margin: 0.2rem;
}

.field-switch {
    input:checked + span {
        transition: background 0.3s;
    }

    .display {
        width: calc(var(--n-width) * 2);
        height: var(--n-width);
        border-radius: 0.3rem;
        var {
            width: calc(var(--n-width) - var(--margin) * 2);
            height: calc(var(--n-width) - var(--margin) * 2);
            top: var(--margin);
            left: var(--margin);
            transition: left 0.3s;
        }
        & > span:nth-of-type(1) {
            position: absolute;
            width: calc(var(--n-width) - var(--margin));
            height: calc(var(--n-width) - var(--margin) * 2);
            top: var(--margin);
            left: var(--margin);
            display: flex;
            justify-content: center;
            align-items: center;
        }
        & > span:nth-of-type(2) {
            position: absolute;
            width: calc(var(--n-width) - var(--margin));
            height: calc(var(--n-width) - var(--margin) * 2);
            top: var(--margin);
            left: calc(100% - var(--n-width));
            display: flex;
            justify-content: center;
            align-items: center;
        }
    }

    input:checked + .display var {
        left: calc(var(--n-width) + var(--margin));
        transition: left 0.3s;
    }

    /* --------------------------------- small size ---------------------------------- */
    .inner-field.h-field-sm {
        input:checked + .display var {
            left: calc(var(--sm-width) + var(--sm-margin));
        }

        .display {
            width: calc(var(--sm-width) * 2);
            height: var(--sm-width);
            var {
                width: calc(var(--sm-width) - var(--sm-margin) * 2);
                height: calc(var(--sm-width) - var(--sm-margin) * 2);
                top: var(--sm-margin);
                left: var(--sm-margin);
            }
            & > span:nth-of-type(1) {
                width: calc(var(--sm-width) - var(--sm-margin));
                height: calc(var(--sm-width) - var(--sm-margin) * 2);
                top: var(--sm-margin);
                left: var(--sm-margin);
            }
            & > span:nth-of-type(2) {
                width: calc(var(--sm-width) - var(--sm-margin));
                height: calc(var(--sm-width) - var(--sm-margin) * 2);
                top: var(--sm-margin);
                left: calc(100% - var(--sm-width));
            }
        }
    }

    /* ------------------------------ very small size -------------------------------- */
    .inner-field.h-field-xs {
        input:checked + .display var {
            left: calc(var(--xs-width) + var(--xs-margin));
        }

        .display {
            width: calc(var(--xs-width) * 2);
            height: var(--xs-width);
            var {
                width: calc(var(--xs-width) - var(--xs-margin) * 2);
                height: calc(var(--xs-width) - var(--xs-margin) * 2);
                top: var(--xs-margin);
                left: var(--xs-margin);
            }
            & > span:nth-of-type(1) {
                width: calc(var(--xs-width) - var(--xs-margin));
                height: calc(var(--xs-width) - var(--xs-margin) * 2);
                top: var(--xs-margin);
                left: var(--xs-margin);
            }
            & > span:nth-of-type(2) {
                width: calc(var(--xs-width) - var(--xs-margin));
                height: calc(var(--xs-width) - var(--xs-margin) * 2);
                top: var(--xs-margin);
                left: calc(100% - var(--xs-width));
            }
        }
    }
}
</style>
