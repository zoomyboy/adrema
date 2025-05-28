<template>
    <div class="fixed shadow-2xl bg-gray-600 right-0 top-0 h-full flex flex-col group is-bright" :class="widths[max]">
        <suspense>
            <slot></slot>

            <template #fallback>
                <div class="flex flex-col h-full">
                    <page-header title="Lade …" @close="$emit('close')"> </page-header>
                    <div class="grow flex items-center justify-center">
                        <ui-spinner class="border-primary-400 w-32 h-32"></ui-spinner>
                    </div>
                </div>
            </template>
        </suspense>
    </div>
</template>

<script setup>
defineEmits(['close']);

const widths = {
    40: 'w-full w-[80vw] max-w-[40rem]',
    30: 'w-full w-[80vw] max-w-[30rem]',
    20: 'w-full w-[80vw] max-w-[20rem]',
    10: 'w-full w-[80vw] max-w-[10rem]',
    0: '',
};

defineProps({
    max: {
        default: () => 40,
        type: Number,
        required: false,
    },
});
</script>
