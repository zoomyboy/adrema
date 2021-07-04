<template>
    <div class="gap-6 grid-cols-4 grid p-6">
        <block title="Ausstehende Mitgliedsbeiträge">
            <div class="text-gray-100">
                <span class="text-xl mr-1 font-semibold" v-text="data.payments.amount"></span>
                <span class="text-sm" v-text="`von ${data.payments.users} / ${data.payments.all_users} Mitgliedern`"></span>
            </div>
        </block>
    </div>
</template>

<script>
import App from '../../layouts/App';
import Block from './Block.vue';

export default {
    layout: App,

    components: { Block },

    props: {
        data: {},
        blocks: {}
    }

};
</script>
