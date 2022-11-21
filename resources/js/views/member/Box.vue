<template>
    <section class="bg-gray-800 p-3 rounded-lg flex flex-col">
        <heading class="col-span-full">{{ heading }}</heading>
        <main :class="containerClass" class="mt-3">
            <slot></slot>
        </main>
    </section>
</template>

<script>
export default {
    props: {
        heading: {
            required: true,
            type: String,
        },
        containerClass: {
            default: function () {
                return '';
            },
        },
    },

    components: {
        heading: () => import('./Heading'),
    },
};
</script>
