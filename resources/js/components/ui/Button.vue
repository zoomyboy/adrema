<template>
    <button v-bind="$attrs" class="btn btn-primary relative group">
        <div :class="{hidden: !isLoading, flex: isLoading}" class="absolute items-center top-0 h-full left-0 ml-2">
            <ui-spinner class="border-primary-400 w-6 h-6 group-hover:border-primary-200" />
        </div>
        <slot />
    </button>
</template>

<script>
export default {
    props: {
        isLoading: {
            type: Boolean,
            default: false,
        },
    },
    data: function () {
        return {};
    },
};
</script>
