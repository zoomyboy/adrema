<template>
    <div class="grow bg-gray-900 flex flex-col transition-all" :class="{'ml-56': menuStore.visible, 'ml-0': !menuStore.visible}">
        <Head :title="$page.props.title"></Head>
        <page-header :title="$page.props.title">
            <template #before-title>
                <a href="#" @click.prevent="menuStore.toggle()" class="mr-2 lg:hidden">
                    <ui-sprite src="menu" class="text-gray-100 w-5 h-5"></ui-sprite>
                </a>
            </template>
            <template #toolbar>
                <slot name="toolbar"></slot>
            </template>
            <template #right>
                <slot name="right"></slot>
            </template>
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
