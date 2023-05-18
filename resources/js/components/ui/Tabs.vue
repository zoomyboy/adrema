<template>
    <div class="p-6 bg-gray-700 border-r border-gray-600 flex-none w-maxc flex flex-col justify-between">
        <div class="grid gap-1">
            <a
                v-for="(item, index) in entries"
                :key="index"
                href="#"
                @click.prevent="openMenu(index)"
                class="rounded py-1 px-3 text-gray-400"
                :class="index === value ? `bg-gray-600` : ''"
                v-text="item.title"
            ></a>
        </div>
        <slot name="bottom"></slot>
    </div>
</template>

<script>
export default {
    props: {
        value: {},
        entries: {},
    },
    methods: {
        openMenu(index) {
            this.$emit('input', index);
        },
    },
};
</script>
