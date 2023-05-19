<template>
    <div class="h-16 px-6 flex justify-between items-center border-b border-solid border-gray-600 group-[.is-bright]:border-gray-500">
        <slot name="before-title"></slot>
        <div class="flex items-center">
            <page-title class="mr-2">{{ title }}</page-title>
            <slot name="toolbar"></slot>
        </div>
        <div class="flex items-center ml-4">
            <a href="#" v-if="$listeners.close" @click.prevent="$emit('close')" class="btn label btn-primary-light icon">
                <svg-sprite class="w-3 h-3" src="close"></svg-sprite>
            </a>
            <slot name="right"></slot>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        title: {
            default: function () {
                return '';
            },
        },
    },
};
</script>
