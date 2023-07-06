<template>
    <div>
        <div v-for="(group, index) in inner.groups" :key="index" class="flex mt-2 items-center leading-none text-gray-100">
            <ui-sprite class="w-4 h-4 mr-2" src="lilie" :class="`text-${group.slug}`"></ui-sprite>
            <span v-text="group.name" class="grow"></span>
            <span v-text="group.count"></span>
        </div>
    </div>
</template>

<script>
export default {
    data: function () {
        return {
            inner: {
                groups: [],
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
