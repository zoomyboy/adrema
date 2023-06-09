<template>
    <div class="sidebar flex flex-col group is-bright">
        <page-header @close="$emit('close')" title="Zahlungen">
            <template #toolbar>
                <page-toolbar-button @click.prevent="create" color="primary" icon="plus" v-if="single === null">Neue Zahlung</page-toolbar-button>
                <page-toolbar-button @click.prevent="cancel" color="primary" icon="undo" v-if="single !== null">Zurück</page-toolbar-button>
            </template>
        </page-header>

        <form v-if="single" class="p-6 grid gap-4 justify-start" @submit.prevent="submit">
            <f-text id="nr" v-model="single.nr" label="Jahr" required></f-text>
            <f-select id="subscription_id" name="subscription_id" :options="subscriptions" v-model="single.subscription_id" label="Beitrag" required></f-select>
            <f-select id="status_id" name="status_id" :options="statuses" v-model="single.status_id" label="Status" required></f-select>
            <button type="submit" class="btn btn-primary">Absenden</button>
        </form>

        <div class="grow" v-else>
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
                            @click.prevent="
                                single = payment;
                                mode = 'edit';
                            "
                            class="inline-flex btn btn-warning btn-sm"
                            ><ui-sprite src="pencil"></ui-sprite
                        ></a>
                        <i-link v-show="!payment.is_accepted" href="#" @click.prevent="accept(payment)" class="inline-flex btn btn-success btn-sm"><ui-sprite src="check"></ui-sprite></i-link>
                        <i-link href="#" @click.prevent="remove(payment)" class="inline-flex btn btn-danger btn-sm"><ui-sprite src="trash"></ui-sprite></i-link>
                    </td>
                </tr>
            </table>
        </div>
        <div class="flex flex-col pb-6 px-6">
            <a
                href="#"
                @click.prevent="openLink(link)"
                :class="{disabled: link.disabled}"
                target="_BLANK"
                v-for="(link, index) in value.payment_links"
                :key="index"
                class="mt-1 text-center btn btn-primary"
                v-text="link.label"
            ></a>
        </div>
    </div>
</template>

<script>
export default {
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

    props: {
        value: {},
        subscriptions: {},
        statuses: {},
    },
};
</script>
