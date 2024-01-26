<template>
    <div class="fixed z-40 top-0 left-0 w-full h-full flex items-center justify-center p-6">
        <div class="relative rounded-lg p-8 bg-zinc-800 shadow-2xl shadow-black border border-zinc-700 border-solid w-full max-h-full overflow-auto" :class="full ? 'h-full' : innerWidth">
            <div class="absolute top-0 right-0 mt-6 mr-6 flex space-x-6">
                <slot name="actions"></slot>
                <a href="#" @click.prevent="$emit('close')">
                    <ui-sprite src="close" class="text-zinc-400 w-6 h-6"></ui-sprite>
                </a>
            </div>
            <h3 v-if="heading" class="font-semibold text-primary-200 text-xl" v-html="heading"></h3>
            <div class="text-primary-100 group is-popup">
                <slot></slot>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        heading: {
            type: String,
            default: () => '',
        },
        innerWidth: {
            default: () => 'max-w-xl',
            type: String,
        },
        full: {
            type: Boolean,
            default: () => false,
        },
    },
};
</script>
