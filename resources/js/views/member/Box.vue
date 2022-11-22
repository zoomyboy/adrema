<template>
    <section class="bg-gray-800 p-3 rounded-lg flex flex-col">
        <heading class="col-span-full" v-if="heading">{{ heading }}</heading>
        <main :class="{'mt-3': heading, 'containerClass': true}">
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
