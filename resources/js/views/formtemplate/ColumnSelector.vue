<template>
    <div class="space-y-1">
        <span class="text-xs font-semibold text-gray-400">Spalten</span>
        <div class="flex rounded-lg border border-gray-500 divide-x divide-gray-500 overflow-hidden">
            <div class="bg-gray-600 w-8 h-6 flex items-center justify-center">
                <ui-sprite src="mobile" class="w-4 h-4"></ui-sprite>
            </div>
            <div
                v-for="(i, index) in 2"
                class="hover:cursor-pointer grow flex items-center justify-center text-sm text-teal-600"
                :class="{'bg-teal-900': i <= modelValue.mobile}"
                @click.prevent="$emit('update:modelValue', {...modelValue, mobile: i})"
                v-text="i"
            ></div>
        </div>

        <div class="flex rounded-lg border border-gray-500 divide-x divide-gray-500 overflow-hidden">
            <div class="bg-gray-600 w-8 h-6 flex items-center justify-center">
                <ui-sprite src="tablet" class="w-4 h-4"></ui-sprite>
            </div>
            <div
                v-for="(i, index) in 4"
                class="hover:cursor-pointer grow flex items-center justify-center text-sm text-teal-600"
                :class="{'bg-teal-900': i <= modelValue.tablet}"
                @click.prevent="$emit('update:modelValue', {...modelValue, tablet: i})"
                v-text="i"
            ></div>
        </div>

        <div class="flex rounded-lg border border-gray-500 divide-x divide-gray-500 overflow-hidden">
            <div class="bg-gray-600 w-8 h-6 flex items-center justify-center">
                <ui-sprite src="desktop" class="w-4 h-4"></ui-sprite>
            </div>
            <div
                v-for="(i, index) in 6"
                class="hover:cursor-pointer grow flex items-center justify-center text-sm text-teal-600"
                :class="{'bg-teal-900': i <= modelValue.desktop}"
                @click.prevent="$emit('update:modelValue', {...modelValue, desktop: i})"
                v-text="i"
            ></div>
        </div>
    </div>
</template>

<script setup>
defineEmits(['update:modelValue']);

const props = defineProps({
    modelValue: {
        required: true,
        validator: (value) => typeof value === 'object' && JSON.stringify(Object.keys(value).sort()) === JSON.stringify(['desktop', 'mobile', 'tablet'].sort()),
    },
});
</script>
