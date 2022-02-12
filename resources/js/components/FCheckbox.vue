<template>
    <label class="flex flex-col relative field-checkbox cursor-pointer" :for="id" :class="{[`size-${size}`]: true}">
        <span v-if="label && inset" class="z-10 absolute top-0 left-0 -mt-2 px-1 ml-3 inset-bg font-semibold text-gray-700">{{ label }}</span>
        <div class="relative flex items-start">
            <input :id="id" type="checkbox" v-model="v" :disabled="disabled" class="invisible absolute" />
            <span class="display-wrapper flex items-center">
                <span class="relative cursor-pointer flex flex-none justify-center items-center display" :class="{'bg-terminoto-2': v === true, 'bg-white': v === false}">
                    <sprite src="check" class="w-4 h-4 check-icon text-white"></sprite>
                </span>
            </span>
            <span v-if="label && !inset" class="text-sm leading-tight ml-3 text-gray-700 checkbox-label flex items-center">
                <span>
                    <span v-text="label" v-if="!html"></span>
                    <span v-html="label" v-if="html"></span>
                    <span v-show="required" class="font-semibold text-red-700">*</span>
                </span>
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
        html: {
            type: Boolean,
            default: false
        },
        required: {
            type: Boolean,
            default: false
        },
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
        }
    },

    created() {
        if (typeof this.items === 'undefined') {
            this.$emit('input', false);
        }
    }
};
</script>

<style lang="css">
:root {
    --checkbox-width: 30px;
    --margin: 0.2rem;
}

.field-checkbox {
    input:checked + span {
        transition: background 0.2s;
    }

    .display-wrapper, .checkbox-label {
        min-height: 34px;
    }

    .display {
        width: var(--checkbox-width);
        height: var(--checkbox-width);
        border-radius: 0.3rem;
        border: solid 2px hsl(60.0, 1.8%, 10.8%);
        .check-icon {
            opacity: 0;
            transition: opacity 0.2s;
        }
    }
    input:checked + .display-wrapper .display .check-icon {
        opacity: 1;
        transition: opacity 0.2s;
    }
}
</style>
