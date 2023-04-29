<template>
    <div class="grow bg-gray-900 flex flex-col transition-all" :class="{'ml-56': menuStore.visible, 'ml-0': !menuStore.visible}">
        <div class="h-16 px-6 flex items-center space-x-3 border-b border-gray-600">
            <a href="#" @click.prevent="menuStore.toggle()" class="lg:hidden">
                <svg-sprite src="menu" class="text-gray-100 w-5 h-5"></svg-sprite>
            </a>
            <span class="text-sm md:text-xl font-semibold text-white leading-none" v-html="$page.props.title"></span>
            <!--
            <i-link v-for="(link, index) in filterMenu" :key="index" :href="link.href" class="btn label mr-2" :class="`btn-${link.color}`" v-tooltip="tooltipsVisible ? link.label : ''">
                <svg-sprite v-show="link.icon" class="w-3 h-3 xl:mr-2" :src="link.icon"></svg-sprite>
                <span class="hidden xl:inline" v-text="link.label"></span>
            </i-link>
            -->
            <div class="flex grow justify-between">
                <portal-target name="toolbar-left"> </portal-target>
                <portal-target name="toolbar-right"> </portal-target>
            </div>
        </div>

        <div class="grow flex flex-col">
            <slot></slot>
        </div>
    </div>
</template>

<script>
import {menuStore} from '../../stores/menuStore.js';

export default {
    data: function () {
        return {
            menuStore: menuStore(),
        };
    },
};
</script>
