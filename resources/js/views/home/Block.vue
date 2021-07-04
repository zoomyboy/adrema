<template>
    <div class="rounded-lg bg-gray-700 text-gray-400 p-3">
        <div class="text-base pb-2 leading-none" v-html="title"></div>
        <slot></slot>
    </div>
</template>

<script>
export default {
    props: {
        title: {
            required: true
        }
    }
};
</script>
