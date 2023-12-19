<template>
    <button v-if="$attrs.onClick" v-tooltip="menuStore.tooltipsVisible ? slotContent : ''" class="btn label" v-bind="$attrs" :class="colors[color]">
        <ui-sprite v-show="icon" class="w-3 h-3 xl:mr-2" :src="icon"></ui-sprite>
        <span class="hidden xl:inline">
            <slot></slot>
        </span>
    </button>
    <i-link v-if="!$attrs.onClick && !asA" v-tooltip="menuStore.tooltipsVisible ? slotContent : ''" :href="href" class="btn label" v-bind="$attrs" :class="colors[color]">
        <ui-sprite v-show="icon" class="w-3 h-3 xl:mr-2" :src="icon"></ui-sprite>
        <span class="hidden xl:inline">
            <slot></slot>
        </span>
    </i-link>
    <a v-if="asA" v-tooltip="menuStore.tooltipsVisible ? slotContent : ''" :href="href" target="_BLANK" class="btn label" v-bind="$attrs" :class="colors[color]">
        <ui-sprite v-show="icon" class="w-3 h-3 xl:mr-2" :src="icon"></ui-sprite>
        <span class="hidden xl:inline">
            <slot></slot>
        </span>
    </a>
</template>

<script>
import {menuStore} from '../../stores/menuStore.js';

export default {
    props: {
        asA: {
            type: Boolean,
            default: () => false,
        },
        href: {
            required: false,
            default: () => '#',
        },
        icon: {},
        color: {},
    },
    data: function () {
        return {
            menuStore: menuStore(),
            colors: {
                primary: 'btn-primary',
                warning: 'btn-warning',
                info: 'btn-info',
            },
        };
    },

    computed: {
        slotContent() {
            return this.$slots.default()[0].children;
        },
    },
};
</script>
