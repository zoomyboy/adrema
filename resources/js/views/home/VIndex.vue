<template>
    <div class="gap-6 grid-cols-4 grid p-6">
        <v-block v-for="(block, index) in blocks" :key="index" :title="block.title">
            <v-component :data="block.data" :is="block.component"></v-component>
        </v-block>
        <!--
        <v-block title="Ausstehende Mitgliedsbeiträge">
        </v-block>
        <v-block title="Gruppierungs-Verteilung">
        </v-block>
        <v-block title="Endende Schhnupperzeiten">
        </v-block>
        -->
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
        'testers': () => import('./Testers.vue'),
        'member-payment': () => import('./MemberPayment.vue'),
    },
};
</script>
