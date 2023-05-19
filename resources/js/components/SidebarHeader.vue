<template>
    <div class="h-16 px-6 flex justify-between items-center border-b border-solid border-gray-500">
        <div class="flex items-center">
            <span class="mr-1 text-xl font-semibold leading-none text-white" v-html="title"></span>
            <slot name="toolbar"></slot>
        </div>
        <div class="flex ml-4">
            <a href="#" @click.prevent="$emit('close')" class="btn label btn-primary-light icon">
                <svg-sprite class="w-3 h-3" src="close"></svg-sprite>
            </a>
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
