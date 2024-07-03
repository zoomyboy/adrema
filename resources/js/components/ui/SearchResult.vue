<template>
    <div>
        <div class="flex items-center justify-between hover:bg-sky-600/20 transition text-sky-300 px-3 sm:px-6 py-1 sm:py-3 rounded-lg">
            <div class="flex space-x-2 items-center">
                <div class="w-5 sm:w-16 flex flex-none">
                    <ui-age-groups icon-class="w-4 h-4 sm:w-6 sm:h-6" class="flex-col sm:flex-row" :member="member"></ui-age-groups>
                </div>
                <div class="flex items-baseline flex-col md:flex-row">
                    <span class="text-lg" v-text="member.fullname"></span>
                    <span class="ml-2 text-xs" v-text="member.group_name"></span>
                </div>
            </div>
            <div class="flex space-x-2">
                <slot name="buttons"></slot>
            </div>
        </div>
    </div>
</template>

<script lang="js" setup>
defineProps({
    member: {
        type: Object,
        required: true
    },
});

const emit = defineEmits(['click']);
</script>
