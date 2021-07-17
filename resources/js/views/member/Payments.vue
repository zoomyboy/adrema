<template>
    <div class="sidebar flex flex-col">
        <sidebar-header :links="value.links" @close="$inertia.visit('/member')" title="Zahlungen"></sidebar-header>

        <div class="custom-table custom-table-light custom-table-sm text-sm flex-grow">
            <header>
                <div>Nr</div>
                <div>Status</div>
                <div>Beitrag</div>
                <div></div>
            </header>

            <div v-for="payment, index in value.data.payments">
                <div v-text="payment.nr"></div>
                <div v-text="payment.status_name"></div>
                <div v-text="payment.subscription_name"></div>
                <div class="flex">
                    <inertia-link :href="`/member/${value.data.id}/payment/${payment.id}/edit`" class="inline-flex btn btn-warning btn-sm"><sprite src="pencil"></sprite></inertia-link>
                    <inertia-link v-show="!payment.is_accepted" href="#" @click.prevent="accept(payment)" class="inline-flex btn btn-success btn-sm"><sprite src="check"></sprite></inertia-link>
                    <inertia-link href="#" @click.prevent="remove(payment)" class="inline-flex btn btn-danger btn-sm"><sprite src="trash"></sprite></inertia-link>
                </div>
            </div>
        </div>
        <div class="flex flex-col pb-6 px-6">
            <a href="#" @click.prevent="openLink(link)" :class="{'disabled': link.disabled}" target="_BLANK" v-for="link in value.payment_links" class="mt-1 text-center btn btn-primary" v-text="link.label"></a>
        </div>
    </div>
</template>

<script>
import SidebarHeader from '../../components/SidebarHeader.vue';

export default {
    components: { SidebarHeader },

    methods: {
        remove(payment) {
            this.$inertia.delete(`/member/${this.value.data.id}/payment/${payment.id}`);
        },

        accept(payment) {
            this.$inertia.patch(`/member/${this.value.data.id}/payment/${payment.id}`, { ...payment, status_id: 3 });
        },

        openLink(link) {
            if (link.disabled) {
                return;
            }

            window.open(link.href);
        }
    },

    props: {
        value: {}
    }
};
</script>
