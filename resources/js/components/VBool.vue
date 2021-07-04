<template>
    <div class="bool" :class="value ? 'enabled' : 'disabled'">
        <sprite v-if="!$slots.default" :src="value ? 'check' : 'close'"></sprite>
        <slot></slot>
    </div>
</template>

<script>
export default {
    props: {
        value: {}
    },

    computed: {
        t() {
            return this.value ? 'Ja' : 'Nein';
        }
    }
};
</script>
