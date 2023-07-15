<template>
    <div class="min-w-[16rem] sm:min-w-[18rem] md:min-w-[24rem] bg-gray-800 rounded-xl overflow-hidden shadow-lg" :class="{'p-6 md:p-10': banner === false}">
        <slot name="heading"></slot>
        <div :class="{'p-6 md:p-10': banner === true}">
            <slot></slot>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        banner: {
            type: Boolean,
            default: () => false,
        },
    },
};
</script>
