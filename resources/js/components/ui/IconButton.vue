<template>
    <button v-on="$listeners" type="button" class="btn label btn-primary">
        <svg-sprite class="w-3 h-3 mr-2" :src="icon"></svg-sprite>
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
