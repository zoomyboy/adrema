<template>
    <div class="sidebar">
        <sidebar-header :links="value.links" @close="$inertia.visit('/member')" title="Zahlungen"></sidebar-header>

        <form class="p-6 grid gap-4 justify-start" @submit.prevent="submit">
            <f-text id="nr" v-model="inner.nr" label="Jahr" required></f-text>
            <f-select id="subscription_id" :options="value.subscriptions" v-model="inner.subscription_id" label="Beitrag" required></f-select>
            <f-select id="status_id" :options="value.statuses" v-model="inner.status_id" label="Status" required></f-select>
            <button type="submit" class="btn btn-primary">Absenden</button>
        </form>
    </div>
</template>

<script>
import SidebarHeader from '../../components/SidebarHeader.vue';

export default {

    data: function() {
        return {
            inner: {},
        };
    },

    components: { SidebarHeader },

    props: {
        value: {}
    },

    methods: {
        submit() {
            this.value.mode === 'create' 
                ? this.$inertia.post(`/member/${this.value.data.id}/payment`, this.inner)
                : this.$inertia.patch(`/member/${this.value.data.id}/payment/${this.inner.id}`, this.inner);
        }
    },

    created() {
        this.inner = this.value.model;
    }

};
</script>
