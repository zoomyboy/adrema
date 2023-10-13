<template>
    <div class="fixed w-full w-[80vw] max-w-[30rem] shadow-2xl bg-gray-600 right-0 top-0 h-full flex flex-col group is-bright">
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
</script>
