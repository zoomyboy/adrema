<template>
    <div v-tooltip="comment" class="bool" :class="value ? 'enabled' : 'disabled'">
        <ui-sprite v-if="!$slots.default" :src="value ? 'check' : 'close'"></ui-sprite>
        <slot></slot>
    </div>
</template>

<script>
export default {
    props: {
        value: {
            type: Boolean,
            default: () => false,
        },
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
