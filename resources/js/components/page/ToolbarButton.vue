<template>
    <i-link :href="href" v-on="$listeners" class="btn label mr-2" :class="colors[color]" v-tooltip="menuStore.tooltipsVisible ? $slots.default[0].text : ''">
        <svg-sprite v-show="icon" class="w-3 h-3 xl:mr-2" :src="icon"></svg-sprite>
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
        href: {},
        icon: {},
        color: {},
    },
};
</script>
