<template>
    <section
        class="p-3 rounded-lg flex flex-col"
        :class="{'bg-gray-800': second === false, 'bg-gray-700': second === true}"
    >
        <div class="flex items-center">
            <heading class="col-span-full" v-if="heading">{{ heading }}</heading>
            <slot name="in-title"></slot>
        </div>
        <main :class="{'mt-2': heading, 'containerClass': true}">
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
