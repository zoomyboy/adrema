<template>
    <div>
        <table cellspacing="0" cellpadding="0" border="0" class="hidden md:table custom-table custom-table-sm text-sm">
            <thead>
                <th>Nr</th>
                <th>Beitrag</th>
                <th>Status</th>
            </thead>
            <tr v-for="(payment, index) in inner" :key="index">
                <td v-text="payment.nr"></td>
                <td v-text="`${payment.subscription.name} (${payment.subscription.amount_human})`"></td>
                <td v-text="payment.status_name"></td>
            </tr>
        </table>

        <div class="md:hidden grid gap-3">
            <ui-box class="relative" :heading="payment.nr" v-for="(payment, index) in inner" :key="index" second>
                <div class="text-xs text-gray-200" v-text="`${payment.subscription.name} (${payment.subscription.amount_human})`"></div>
                <div class="text-xs text-gray-200" v-text="payment.status_name"></div>
            </ui-box>
        </div>
    </div>
</template>

<script>
export default {
    data: function () {
        return {
            inner: [],
        };
    },

    props: {
        value: {},
    },

    created() {
        this.inner = this.value;
    },
};
</script>
