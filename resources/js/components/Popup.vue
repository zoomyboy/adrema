<template>
    <div class="fixed z-40 top-0 left-0 w-full h-full flex items-center justify-center p-6">
        <div
            class="rounded-lg p-8 bg-zinc-800 shadow-2xl shadow-black border border-zinc-700 border-solid w-full max-w-xl"
        >
            <h3 class="font-semibold text-primary-200 text-xl" v-html="heading"></h3>
            <div class="text-primary-100">
                <slot></slot>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        heading: {},
    },
};
</script>
