<template>
    <div class="grow bg-gray-900 flex flex-col transition-all" :class="{'ml-56': menuStore.visible, 'ml-0': !menuStore.visible}">
        <Head :title="$page.props.title"></Head>
        <page-header :title="$page.props.title">
            <template #before-title>
                <a href="#" class="mr-2 lg:hidden" @click.prevent="menuStore.toggle()">
                    <ui-sprite src="menu" class="text-gray-100 w-5 h-5"></ui-sprite>
                </a>
            </template>
            <template #toolbar>
                <slot name="toolbar"></slot>
            </template>
            <template #right>
                <slot name="right"></slot>
                <div class="flex items-center space-x-2">
                    <div class="rounded-full overflow-hidden border-2 border-solid border-gray-300">
                        <img :src="$page.props.auth.user.avatar_url" class="w-8 h-8 object-cover" />
                    </div>
                    <div class="text-gray-300" v-text="`${$page.props.auth.user.firstname} ${$page.props.auth.user.lastname}`"></div>
                </div>
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
