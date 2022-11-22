<template>
    <div class="gap-6 md:grid-cols-2 xl:grid-cols-4 grid p-6">
        <v-block v-for="(block, index) in blocks" :key="index" :title="block.title">
            <v-component :data="block.data" :is="block.component"></v-component>
        </v-block>
    </div>
</template>

<script>
export default {
    props: {
        data: {},
        blocks: {},
    },

    components: {
        'VBlock': () => import('./VBlock'),
        'age-group-count': () => import('./AgeGroupCount.vue'),
        'efz-pending': () => import('./EfzPending.vue'),
        'ps-pending': () => import('./PsPending.vue'),
        'testers': () => import('./Testers.vue'),
        'member-payment': () => import('./MemberPayment.vue'),
    },
};
</script>
