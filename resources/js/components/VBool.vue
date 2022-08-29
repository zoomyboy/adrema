<template>
    <div class="bool" v-tooltip="comment" :class="value ? 'enabled' : 'disabled'">
        <svg-sprite v-if="!$slots.default" :src="value ? 'check' : 'close'"></svg-sprite>
        <slot></slot>
    </div>
</template>

<script>
export default {
    props: {
        value: {},
        trueComment: {},
        falseComment: {},
    },

    computed: {
        t() {
            return this.value ? 'Ja' : 'Nein';
        },
        comment() {
            if (this.value && this.trueComment) {
                return this.trueComment;
            }
            if (!this.value && this.falseComment) {
                return this.falseComment;
            }

            return '';
        },
    },
};
</script>
