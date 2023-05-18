<template>
    <div class="flex justify-center">
        <div class="btn btn-sm label primary" v-text="value" v-if="value"></div>
        <div class="text-xs" v-text="fallback" v-else></div>
    </div>
</template>

<script>
export default {
    props: {
        value: {},
        fallback: {},
    },
};
</script>
