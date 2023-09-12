<template>
    <div v-tooltip="longLabel" class="flex space-x-2 items-center">
        <div class="border-2 rounded-full w-5 h-5 flex items-center justify-center" :class="value ? (dark ? 'border-green-500' : 'border-green-700') : dark ? 'border-red-500' : 'border-red-700'">
            <ui-sprite :src="value ? 'check' : 'close'" :class="value ? (dark ? 'text-green-600' : 'text-green-800') : dark ? 'text-red-600' : 'text-red-800'" class="w-3 h-3 flex-none"></ui-sprite>
        </div>
        <div class="text-gray-400 text-xs" v-text="label"></div>
    </div>
</template>

<script>
export default {
    props: {
        value: {
            required: true,
            type: Boolean,
        },
        label: {
            type: String,
            default: () => '',
        },
        longLabel: {
            default: function () {
                return null;
            },
        },
        dark: {
            type: Boolean,
            default: () => false,
        },
    },
};
</script>
