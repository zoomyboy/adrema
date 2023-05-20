<template>
    <div class="flex gap-3">
        <div class="grid gap-3">
            <ui-text-display class="col-start-1" label="Führungszeugnis eingesehen" :value="inner.efz_human ? inner.efz_human : 'nie'"></ui-text-display>
            <ui-text-display class="col-start-1" label="Präventionsschulung" :value="inner.ps_at_human ? inner.ps_at_human : 'nie'"></ui-text-display>
            <ui-text-display class="col-start-1" label="Vertiefungsschulung" :value="inner.more_ps_at_human ? inner.more_ps_at_human : 'nie'"></ui-text-display>
            <ui-text-display class="col-start-1" label="Einsatz ohne Schulung" :value="inner.without_education_at_human ? inner.without_education_at_human : 'nie'"></ui-text-display>
            <ui-text-display class="col-start-1" label="Einsatz ohne EFZ" :value="inner.without_efz_at_human ? inner.without_efz_at_human : 'nie'"></ui-text-display>
        </div>
        <div class="grid gap-3 content-start">
            <ui-boolean-display :value="inner.has_vk" long-label="Verhaltenskodex unterschrieben" label="VK"></ui-boolean-display>
            <ui-boolean-display :value="inner.has_svk" long-label="SVK unterschrieben" label="SVK"></ui-boolean-display>
            <ui-boolean-display :value="inner.multiply_pv" long-label="Multiplikator*in Präventionsschulung" label="Multipl. PS"></ui-boolean-display>
            <ui-boolean-display :value="inner.multiply_more_pv" long-label="Multiplikator*in Vertierungsschulung" label="Multipl. VS"></ui-boolean-display>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        inner: {},
    },
};
</script>
