<template>
    <ui-box class="relative" :heading="heading" container-class="grid gap-3">
        <slot></slot>
        <template #in-title>
            <div class="flex ml-3 space-x-3 absolute right-0 top-0 mr-4 mt-4">
                <a v-if="storeable" href="#" @click.prevent="$emit('submit')">
                    <ui-sprite src="save" class="text-zinc-400 w-4 h-4"></ui-sprite>
                </a>
                <a v-if="closeable" href="#" @click.prevent="$emit('close')">
                    <ui-sprite src="close" class="text-zinc-400 w-4 h-4"></ui-sprite>
                </a>
            </div>
        </template>
    </ui-box>
</template>

<script lang="js" setup>
const emit = defineEmits(['close', 'submit']);

const props = defineProps({
    heading: {},
    closeable: {
        default: () => true,
        type: Boolean,
    },
    storeable: {
        default: () => true,
        type: Boolean,
    },
});
</script>
