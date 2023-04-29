<template>
    <div>
        <div class="text-gray-100">
            <span class="text-xl mr-1 font-semibold" v-text="inner.amount"></span>
            <span class="text-sm" v-text="`von ${inner.members} / ${inner.total_members} Mitgliedern`"></span>
        </div>
    </div>
</template>

<script>
export default {
    data: function () {
        return {
            inner: {},
        };
    },

    props: {
        data: {},
    },

    created() {
        this.inner = this.data;
    },
};
</script>
