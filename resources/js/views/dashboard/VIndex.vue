<template>
    <page-layout>
        <div class="gap-6 md:grid-cols-2 xl:grid-cols-4 grid p-6">
            <v-block v-for="(block, index) in blocks" :key="index" :title="block.title">
                <component :is="block.component" :data="block.data"></component>
            </v-block>
        </div>
    </page-layout>
</template>

<script>
import {defineAsyncComponent} from 'vue';

export default {
    props: {
        data: {},
        blocks: {},
    },

    components: {
        'VBlock': defineAsyncComponent(() => import('./VBlock.vue')),
        'age-group-count': defineAsyncComponent(() => import('./AgeGroupCount.vue')),
        'efz-pending': defineAsyncComponent(() => import('./EfzPending.vue')),
        'ps-pending': defineAsyncComponent(() => import('./PsPending.vue')),
        'testers': defineAsyncComponent(() => import('./Testers.vue')),
        'member-payment': defineAsyncComponent(() => import('./MemberPayment.vue')),
    },
};
</script>
