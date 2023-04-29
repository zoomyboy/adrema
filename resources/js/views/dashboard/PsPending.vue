<template>
    <div>
        <div
            v-for="(member, index) in inner.members"
            :key="index"
            class="flex mt-2 items-center leading-none text-gray-100"
        >
            <span class="grow" v-text="`${member.fullname}`"></span>
        </div>
    </div>
</template>

<script>
export default {
    data: function () {
        return {
            inner: {
                members: [],
            },
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
