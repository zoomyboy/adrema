<template>
    <div class="sidebar flex flex-col group is-bright">
        <page-header title="Zahlungen" @close="$emit('close')">
            <template #toolbar>
                <page-toolbar-button v-if="single === null" color="primary" icon="plus" @click.prevent="create">Neue Zahlung</page-toolbar-button>
                <page-toolbar-button v-if="single !== null" color="primary" icon="undo" @click.prevent="cancel">Zurück</page-toolbar-button>
            </template>
        </page-header>

        <form v-if="single" class="p-6 grid gap-4 justify-start" @submit.prevent="submit">
            <f-text id="nr" v-model="single.nr" label="Jahr" required></f-text>
            <f-select id="subscription_id" v-model="single.subscription_id" name="subscription_id" :options="subscriptions" label="Beitrag" required></f-select>
            <f-select id="status_id" v-model="single.status_id" name="status_id" :options="statuses" label="Status" required></f-select>
            <button type="submit" class="btn btn-primary">Absenden</button>
        </form>

        <div v-else class="grow">
            <table class="custom-table custom-table-light custom-table-sm text-sm">
                <thead>
                    <th>Nr</th>
                    <th>Status</th>
                    <th>Beitrag</th>
                    <th></th>
                </thead>

                <tr v-for="(payment, index) in value.payments" :key="index">
                    <td v-text="payment.nr"></td>
                    <td v-text="payment.status_name"></td>
                    <td v-text="payment.subscription.name"></td>
                    <td class="flex">
                        <a
                            href="#"
                            class="inline-flex btn btn-warning btn-sm"
                            @click.prevent="
                                single = payment;
                                mode = 'edit';
                            "
                            ><ui-sprite src="pencil"></ui-sprite
                        ></a>
                        <i-link v-show="!payment.is_accepted" href="#" class="inline-flex btn btn-success btn-sm" @click.prevent="accept(payment)"><ui-sprite src="check"></ui-sprite></i-link>
                        <i-link href="#" class="inline-flex btn btn-danger btn-sm" @click.prevent="remove(payment)"><ui-sprite src="trash"></ui-sprite></i-link>
                    </td>
                </tr>
            </table>
        </div>
        <div class="flex flex-col pb-6 px-6">
            <a
                v-for="(link, index) in value.payment_links"
                :key="index"
                href="#"
                :class="{disabled: link.disabled}"
                target="_BLANK"
                class="mt-1 text-center btn btn-primary"
                @click.prevent="openLink(link)"
                v-text="link.label"
            ></a>
        </div>
    </div>
</template>

<script>
export default {

    props: {
        value: {},
        subscriptions: {},
        statuses: {},
    },
    data: function () {
        return {
            mode: null,
            single: null,
        };
    },

    methods: {
        create() {
            this.mode = 'create';
            this.single = {};
        },
        cancel() {
            this.mode = this.single = null;
        },
        remove(payment) {
            this.$inertia.delete(`/member/${this.value.id}/payment/${payment.id}`);
        },

        accept(payment) {
            this.$inertia.patch(`/member/${this.value.id}/payment/${payment.id}`, {...payment, status_id: 3});
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
                      },
                  })
                : this.$inertia.patch(`/member/${this.value.id}/payment/${this.single.id}`, this.single, {
                      onFinish() {
                          _self.single = null;
                      },
                  });
        },
    },
};
</script>
