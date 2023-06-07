<template>
    <button class="btn btn-primary relative group">
        <div :class="{hidden: !isLoading, flex: isLoading}" class="absolute items-center top-0 h-full left-0 ml-2">
            <ui-spinner class="border-primary-400 w-6 h-6 group-hover:border-primary-200"></ui-spinner>
        </div>
        <slot></slot>
    </button>
</template>

<script>
import {menuStore} from '../../stores/menuStore.js';

export default {
    data: function () {
        return {};
    },
    props: {
        isLoading: {
            type: Boolean,
            default: false,
        },
    },
};
</script>
