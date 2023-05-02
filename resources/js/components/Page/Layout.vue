<template>
    <div class="grow bg-gray-900 flex flex-col transition-all" :class="{'ml-56': menuStore.visible, 'ml-0': !menuStore.visible}">
        <div class="h-16 px-6 flex items-center space-x-3 border-b border-gray-600">
            <a href="#" @click.prevent="menuStore.toggle()" class="lg:hidden">
                <svg-sprite src="menu" class="text-gray-100 w-5 h-5"></svg-sprite>
            </a>
            <span class="text-sm md:text-xl font-semibold text-white leading-none" v-html="$page.props.title"></span>
            <slot name="toolbar"></slot>
            <div class="flex grow justify-between">
                <portal-target name="toolbar-left"> </portal-target>
                <portal-target name="toolbar-right"> </portal-target>
            </div>
        </div>

        <div :class="pageClass" class="grow flex flex-col">
            <slot></slot>
        </div>
    </div>
</template>

<script>
import {menuStore} from '../../stores/menuStore.js';

export default {
    inheritAttrs: false,
    props: {
        pageClass: {
            default: () => '',
            required: false,
            type: String,
        },
    },
    data: function () {
        return {
            menuStore: menuStore(),
        };
    },
};
</script>
