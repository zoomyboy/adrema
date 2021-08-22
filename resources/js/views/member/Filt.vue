<template>
    <div class="px-6 py-2 flex border-b border-gray-600 space-x-3">
        <f-switch id="ausstand" @input="reload" v-model="value.ausstand" label="Nur Ausstände" size="sm"></f-switch>
        <f-select id="billKinds" @input="reload" :options="billKinds" v-model="value.bill_kind" label="Rechnung" size="sm"></f-select>
    </div>
</template>

<script>
import mergesQueryString from '../../mixins/mergesQueryString.js';

export default {

    mixins: [mergesQueryString],

    props: {
        value: {},
        billKinds: {},
    },

    methods: {
        reload() {
            this.$inertia.visit(this.qs({filter: JSON.stringify(this.value)}), {
                preserveState: true
            });
        }
    }
};
</script>
