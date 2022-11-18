<template>
    <div></div>
</template>

<script>
export default {
    data: function () {
        return {
            inner: {},
        };
    },

    methods: {},

    props: {
        data: {},
    },

    created() {
        this.inner = this.data;
    },
};
</script>
