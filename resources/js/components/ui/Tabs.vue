<template>
    <div class="flex-none w-maxc flex flex-col justify-between border-b-2 border-gray-500 group-[.is-popup]:border-zinc-500 mb-3">
        <div class="flex space-x-1 px-2">
            <a
                v-for="(item, index) in entries"
                :key="index"
                href="#"
                class="rounded-t-lg py-1 px-3 text-zinc-300"
                :class="index === modelValue ? `bg-gray-700 group-[.is-popup]:bg-zinc-600` : ''"
                @click.prevent="openMenu(index)"
                v-text="item.title"
            ></a>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        modelValue: {
            type: Number,
            required: true,
        },
        entries: {
            required: true,
            validator: function (entries) {
                return entries.filter((e) => e.title === undefined || typeof e.title !== 'string' || e.title.length === 0).length === 0;
            },
        },
    },
    emits: ['update:modelValue'],
    methods: {
        openMenu(index) {
            this.$emit('update:modelValue', index);
        },
    },
};
</script>
