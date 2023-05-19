<template>
    <div class="grow bg-gray-900 flex flex-col transition-all" :class="{'ml-56': menuStore.visible, 'ml-0': !menuStore.visible}">
        <page-header :title="$page.props.title">
            <div slot="before-title" class="flex items-center">
                <a href="#" @click.prevent="menuStore.toggle()" class="lg:hidden mr-2">
                    <svg-sprite src="menu" class="text-gray-100 w-5 h-5"></svg-sprite>
                </a>
            </div>
            <div class="flex" slot="toolbar">
                <slot name="toolbar"></slot>
            </div>
            <div slot="right">
                <portal-target name="toolbar-right"> </portal-target>
            </div>
        </page-header>

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
