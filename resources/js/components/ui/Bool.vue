<template>
    <div class="bool" v-tooltip="comment" :class="modelValue ? 'enabled' : 'disabled'">
        <ui-sprite v-if="!$slots.default" :src="modelValue ? 'check' : 'close'"></ui-sprite>
        <slot></slot>
    </div>
</template>

<script>
export default {
    props: {
        modelValue: {},
        trueComment: {},
        falseComment: {},
    },

    computed: {
        t() {
            return this.modelValue ? 'Ja' : 'Nein';
        },
        comment() {
            if (this.modelValue && this.trueComment) {
                return this.trueComment;
            }
            if (!this.modelValue && this.falseComment) {
                return this.falseComment;
            }

            return '';
        },
    },
};
</script>
