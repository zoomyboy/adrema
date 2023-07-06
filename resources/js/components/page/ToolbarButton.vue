<template>
    <button class="btn label" v-bind="$attrs" :class="colors[color]" v-tooltip="menuStore.tooltipsVisible ? slotContent : ''" v-if="$attrs.onClick">
        <ui-sprite v-show="icon" class="w-3 h-3 xl:mr-2" :src="icon"></ui-sprite>
        <span class="hidden xl:inline"><slot></slot></span>
    </button>
    <i-link :href="href" class="btn label" v-bind="$attrs" :class="colors[color]" v-tooltip="menuStore.tooltipsVisible ? slotContent : ''" v-else>
        <ui-sprite v-show="icon" class="w-3 h-3 xl:mr-2" :src="icon"></ui-sprite>
        <span class="hidden xl:inline"><slot></slot></span>
    </i-link>
</template>

<script>
import {menuStore} from '../../stores/menuStore.js';

export default {
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
    props: {
        href: {
            required: false,
            default: () => '#',
        },
        icon: {},
        color: {},
    },

    computed: {
        slotContent() {
            return this.$slots.default()[0].children;
        }
    },
};
</script>
