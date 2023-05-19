<template>
    <div class="grow bg-gray-900 flex flex-col transition-all" :class="{'ml-56': menuStore.visible, 'ml-0': !menuStore.visible}">
        <div class="h-16 px-6 flex justify-between items-center border-b border-solid border-gray-600 group-[.is-bright]:border-gray-500">
            <a href="#" @click.prevent="menuStore.toggle()" class="lg:hidden mr-2">
                <svg-sprite src="menu" class="text-gray-100 w-5 h-5"></svg-sprite>
            </a>
            <div class="flex items-center">
                <page-title class="mr-2">{{ $page.props.title }}</page-title>
                <slot name="toolbar"></slot>
            </div>
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
