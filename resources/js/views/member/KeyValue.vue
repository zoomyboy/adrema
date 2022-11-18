<template>
    <div>
        <div class="text-gray-400 text-xs" v-text="label"></div>
        <div class="text-gray-200 text-sm">
            <span v-if="!type" v-text="value"></span>
            <a
                :href="`mailto:${value}`"
                class="text-teal-500 hover:text-teal-300 transition-all"
                v-text="value"
                v-if="type === 'email'"
            ></a>
            <a
                :href="`tel:${value}`"
                class="text-teal-500 hover:text-teal-300 transition-all"
                v-text="value"
                v-if="type === 'tel'"
            ></a>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        type: {
            default: function () {
                return null;
            },
        },
        value: {
            required: true,
        },
        label: {
            required: true,
        },
    },
};
</script>
