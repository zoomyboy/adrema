<template>
    <div>
        <div
            v-for="(member, index) in inner.members"
            :key="index"
            class="flex mt-2 items-center leading-none text-gray-100"
        >
            <span class="grow" v-text="`${member.name}`"></span>
            <span class="mr-2 text-sm tex-gray-600" v-text="`${member.try_ends_at}`"></span>
            <span class="text-xs tex-gray-600" v-text="`${member.try_ends_at_human}`"></span>
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
