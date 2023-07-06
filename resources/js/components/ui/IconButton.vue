<template>
    <button v-bind="$attrs" type="button" class="btn label btn-primary">
        <ui-sprite class="w-3 h-3 mr-2" :src="icon"></ui-sprite>
        <span><slot></slot></span>
    </button>
</template>

<script>
export default {
    props: {
        icon: {
            required: true,
        },
    },
};
</script>
