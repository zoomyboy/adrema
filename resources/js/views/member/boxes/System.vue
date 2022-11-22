<template>
    <div class="grid gap-3">
        <key-value v-show="inner.nami_id" label="Nami Mitgliedsnummer" :value="inner.nami_id"></key-value>
        <key-value label="Beitrag" :value="inner.subscription ? inner.subscription.name : 'kein'"></key-value>
        <key-value v-if="inner.joined_at_human" label="Eintrittsdatum" :value="inner.joined_at_human"></key-value>
        <key-value v-if="inner.bill_kind_name" label="Rechnung" :value="inner.bill_kind_name"></key-value>
        <boolean :value="inner.send_newspaper" label="Mittendrin versenden"></boolean>
    </div>
</template>

<script>
export default {
    props: {
        inner: {},
    },
    components: {
        'key-value': () => import('../KeyValue'),
        'boolean': () => import('../Boolean'),
    },
};
</script>
