<template>
    <section class="p-3 rounded-lg flex flex-col"
        :class="{ 'bg-gray-800 group-[.is-popup]:bg-zinc-700': second === false, 'bg-gray-700 group-[.is-popup]:bg-zinc-600': second === true }">
        <div class="flex items-center">
            <div v-if="heading" class="col-span-full font-semibold text-gray-300 group-[.is-popup]:text-zinc-300"
                v-text="heading"></div>
            <slot name="in-title"></slot>
        </div>
        <main :class="{ 'mt-2': heading, [containerClass]: true }">
            <slot></slot>
        </main>
    </section>
</template>

<script>
export default {
    props: {
        heading: {
            type: String,
        },
        second: {
            type: Boolean,
            default: false,
        },
        containerClass: {
            default: function () {
                return '';
            },
        },
    },
};
</script>
