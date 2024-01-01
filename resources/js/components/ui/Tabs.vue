<template>
    <div class="flex-none w-maxc flex flex-col justify-between border-b-2 group-[.is-popup]:border-zinc-500 mb-3">
        <div class="flex space-x-1 px-2">
            <a v-for="(item, index) in entries" :key="index" href="#" class="rounded-t-lg py-1 px-3 text-zinc-300"
                :class="index === modelValue ? `group-[.is-popup]:bg-zinc-600` : ''" @click.prevent="openMenu(index)"
                v-text="item.title"></a>
        </div>
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
