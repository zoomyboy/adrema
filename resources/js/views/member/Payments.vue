<template>
    <div class="sidebar flex flex-col">
        <sidebar-header :links="indexLinks" @close="$emit('close')" @create="mode = 'create'; single = {}" title="Zahlungen"></sidebar-header>

        <form v-if="single" class="p-6 grid gap-4 justify-start" @submit.prevent="submit">
            <f-text id="nr" v-model="single.nr" label="Jahr" required></f-text>
            <f-select id="subscription_id" :options="subscriptions" v-model="single.subscription_id" label="Beitrag" required></f-select>
            <f-select id="status_id" :options="statuses" v-model="single.status_id" label="Status" required></f-select>
            <button type="submit" class="btn btn-primary">Absenden</button>
        </form>

        <table v-else class="custom-table custom-table-light custom-table-sm text-sm flex-grow">
            <thead>
                <th>Nr</th>
                <th>Status</th>
                <th>Beitrag</th>
                <th></th>
            </thead>

            <tr v-for="payment, index in value.payments">
                <td v-text="payment.nr"></td>
                <td v-text="payment.status_name"></td>
                <td v-text="payment.subscription_name"></td>
                <td class="flex">
                    <a href="#" @click.prevent="single = payment; mode = 'edit'" class="inline-flex btn btn-warning btn-sm"><sprite src="pencil"></sprite></a>
                    <inertia-link v-show="!payment.is_accepted" href="#" @click.prevent="accept(payment)" class="inline-flex btn btn-success btn-sm"><sprite src="check"></sprite></inertia-link>
                    <inertia-link href="#" @click.prevent="remove(payment)" class="inline-flex btn btn-danger btn-sm"><sprite src="trash"></sprite></inertia-link>
                </td>
            </tr>
        </table>
        <div class="flex flex-col pb-6 px-6">
            <a href="#" @click.prevent="openLink(link)" :class="{'disabled': link.disabled}" target="_BLANK" v-for="link in value.payment_links" class="mt-1 text-center btn btn-primary" v-text="link.label"></a>
        </div>
    </div>
</template>

<script>
import SidebarHeader from '../../components/SidebarHeader.vue';

export default {
    data: function() {
        return {
            mode: null,
            single: null,
            indexLinks: [
                {event: 'create', label: 'Neue Zahlung'}
            ]
        };
    },

    components: { SidebarHeader },

    methods: {
        remove(payment) {
            this.$inertia.delete(`/member/${this.value.id}/payment/${payment.id}`);
        },

        accept(payment) {
            this.$inertia.patch(`/member/${this.value.id}/payment/${payment.id}`, { ...payment, status_id: 3 });
        },

        openLink(link) {
            if (link.disabled) {
                return;
            }

            window.open(link.href);
        },

        submit() {
            var _self = this;

            this.mode === 'create' 
                ? this.$inertia.post(`/member/${this.value.id}/payment`, this.single, {
                    onFinish() {
                        _self.single = null;
                    }
                })
                : this.$inertia.patch(`/member/${this.value.id}/payment/${this.single.id}`, this.single, {
                    onFinish() {
                        _self.single = null;
                    }
                });
        }
    },

    props: {
        value: {},
        subscriptions: {},
        statuses: {},
    }
};
</script>
