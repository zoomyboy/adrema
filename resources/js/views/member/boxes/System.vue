<template>
    <div class="grid gap-3">
        <ui-text-display v-show="inner.nami_id" label="NaMi ID" :value="inner.nami_id"></ui-text-display>
        <ui-text-display v-show="inner.nami_id" label="NaMi Mitgliedsnummer" :value="inner.mitgliedsnr"></ui-text-display>
        <ui-text-display label="Beitrag" :value="inner.subscription ? inner.subscription.name : 'kein'"></ui-text-display>
        <ui-text-display v-if="inner.joined_at_human" label="Eintrittsdatum" :value="inner.joined_at_human"></ui-text-display>
        <ui-text-display v-if="inner.bill_kind_name" label="Rechnung" :value="inner.bill_kind_name"></ui-text-display>
        <ui-boolean-display :value="inner.send_newspaper" label="Mittendrin versenden"></ui-boolean-display>
    </div>
</template>

<script>
export default {
    props: {
        inner: {},
    },
};
</script>
