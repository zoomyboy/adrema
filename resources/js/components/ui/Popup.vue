<template>
    <div class="fixed z-40 top-0 left-0 w-full h-full flex items-center justify-center p-6 bg-black/60 backdrop-blur-sm">
        <div class="relative rounded-lg p-8 bg-zinc-800 shadow-2xl shadow-black border border-zinc-700 border-solid w-full max-h-full flex flex-col overflow-auto"
             :class="full ? 'h-full' : innerWidth"
        >
            <div class="absolute top-0 right-0 mt-6 mr-6 flex space-x-6">
                <slot name="actions" />
                <a href="#" @click.prevent="$emit('close')">
                    <ui-sprite src="close" class="text-zinc-400 w-6 h-6" />
                </a>
            </div>
            <div class="flex justify-center">
                <ui-sprite v-if="icon" class="text-yellow-700 size-28" :src="icon" />
            </div>
            <h3 v-if="heading" class="font-semibold text-primary-200 text-xl" :class="{'text-center mt-5': icon !== null}" v-html="heading" />
            <div class="text-primary-100 group is-popup grow flex flex-col">
                <suspense>
                    <div>
                        <slot />
                    </div>
                    <template #fallback>
                        <ui-loading />
                    </template>
                </suspense>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
defineEmits<{
    close: [],
}>();
withDefaults(defineProps<{
    visible: boolean,
    heading?: string,
    innerWidth?: string,
    full?: boolean,
    icon?: string|null,
}>(), {innerWidth: 'max-w-xl', full: false, heading: '', icon: null});
</script>
