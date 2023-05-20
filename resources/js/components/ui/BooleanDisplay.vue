<template>
    <div v-tooltip="longLabel" class="flex space-x-2 items-center">
        <div class="border-2 rounded-full w-5 h-5 flex items-center justify-center" :class="value ? 'border-green-700' : 'border-red-700'">
            <svg-sprite :src="value ? 'check' : 'close'" :class="value ? 'text-green-800' : 'text-red-800'" class="w-3 h-3 flex-none"></svg-sprite>
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
            required: true,
            type: String,
        },
        longLabel: {
            default: function () {
                return null;
            },
        },
    },
};
</script>
