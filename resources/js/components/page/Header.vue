<template>
    <div class="h-16 px-6 flex items-center justify-between border-b border-solid border-gray-600 group-[.is-bright]:border-gray-500">
        <div class="flex items-center space-x-2">
            <slot name="before-title"></slot>
            <page-title>{{ title }}</page-title>
            <slot name="toolbar"></slot>
        </div>
        <div class="flex items-center space-x-2 ml-2">
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
