<template>
    <div class="flex gap-3">
        <div class="grid gap-3">
            <key-value
                class="col-start-1"
                label="Führungszeugnis eingesehen"
                :value="inner.efz_human ? inner.efz_human : 'nie'"
            ></key-value>
            <key-value
                class="col-start-1"
                label="Präventionsschulung"
                :value="inner.ps_at_human ? inner.ps_at_human : 'nie'"
            ></key-value>
            <key-value
                class="col-start-1"
                label="Vertiefungsschulung"
                :value="inner.more_ps_at_human ? inner.more_ps_at_human : 'nie'"
            ></key-value>
            <key-value
                class="col-start-1"
                label="Einsatz ohne Schulung"
                :value="inner.without_education_at_human ? inner.without_education_at_human : 'nie'"
            ></key-value>
            <key-value
                class="col-start-1"
                label="Einsatz ohne EFZ"
                :value="inner.without_efz_at_human ? inner.without_efz_at_human : 'nie'"
            ></key-value>
        </div>
        <div class="grid gap-3 content-start">
            <boolean :value="inner.has_vk" long-label="Verhaltenskodex unterschrieben" label="VK"></boolean>
            <boolean :value="inner.has_svk" long-label="SVK unterschrieben" label="SVK"></boolean>
            <boolean
                :value="inner.multiply_pv"
                long-label="Multiplikator*in Präventionsschulung"
                label="Multipl. PS"
            ></boolean>
            <boolean
                :value="inner.multiply_more_pv"
                long-label="Multiplikator*in Vertierungsschulung"
                label="Multipl. VS"
            ></boolean>
        </div>
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
