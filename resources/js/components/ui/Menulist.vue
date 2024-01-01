<template>
    <div class="p-6 bg-gray-700 border-r border-gray-600 flex-none w-maxc flex flex-col justify-between">
        <div class="grid gap-1">
            <a v-for="(item, index) in entries" :key="index" href="#" class="rounded py-1 px-3 text-gray-400"
                :class="index === modelValue ? `bg-gray-600` : ''" @click.prevent="openMenu(index)" v-text="item.title"></a>
        </div>
        <slot name="bottom"></slot>
    </div>
</template>

<script>
export default {
    props: {
        modelValue: {},
        entries: {},
    },
    methods: {
        openMenu(index) {
            this.$emit('update:modelValue', index);
        },
    },
};
</script>
