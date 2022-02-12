<template>
    <div class="bool" :class="value ? 'enabled' : 'disabled'">
        <svg-sprite v-if="!$slots.default" :src="value ? 'check' : 'close'"></svg-sprite>
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
