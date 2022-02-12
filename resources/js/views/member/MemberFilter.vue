<template>
    <div class="px-6 py-2 flex border-b border-gray-600 space-x-3">
        <f-switch v-show="hasModule('bill')" id="ausstand" @input="reload" v-model="inner.ausstand" label="Nur Ausstände" size="sm"></f-switch>
        <f-select v-show="hasModule('bill')" id="billKinds" @input="reload" :options="billKinds" v-model="inner.bill_kind" label="Rechnung" size="sm"></f-select>
        <f-select id="activity_id" @input="reload" :options="activities" v-model="inner.activity_id" label="Tätigkeit" size="sm"></f-select>
        <f-select id="subactivity_id" @input="reload" :options="subactivities" v-model="inner.subactivity_id" label="Untertätigkeit" size="sm"></f-select>
    </div>
</template>

<script>
import mergesQueryString from '../../mixins/mergesQueryString.js';

export default {

    data: function() {
        return {
            inner: {}
        };
    },

    mixins: [mergesQueryString],

    props: {
        value: {},
        billKinds: {},
        activities: {},
        subactivities: {},
    },

    methods: {
        reload() {
            this.$inertia.visit(this.qs({filter: JSON.stringify(this.inner)}), {
                preserveState: true
            });
        }
    },

    created() {
        this.inner = this.value;
    }

};
</script>
