<template>
    <div id="app" class="flex font-sans grow">
        <v-notification class="fixed z-40 right-0 bottom-0 mb-3 mr-3"></v-notification>

        <!-- ******************************** Sidebar ******************************** -->
        <div
            class="fixed z-40 bg-gray-800 p-6 w-56 top-0 h-screen border-r border-gray-600 border-solid flex flex-col justify-between transition-all"
            :class="{
                '-left-[14rem]': !(menuVisible || (!menuVisible && menuOverflowVisible)),
                'left-0': menuVisible || (!menuVisible && menuOverflowVisible),
            }"
        >
            <div class="grid gap-2">
                <v-link href="/" menu="dashboard" icon="loss">Dashboard</v-link>
                <v-link href="/member" menu="member" icon="user">Mitglieder</v-link>
                <v-link href="/subscription" v-show="hasModule('bill')" menu="subscription" icon="money">Beiträge</v-link>
                <v-link href="/contribution" menu="contribution" icon="contribution">Zuschüsse</v-link>
                <v-link href="/activity" menu="activity" icon="activity">Tätigkeiten</v-link>
            </div>
            <div class="grid gap-2">
                <v-link href="/setting" menu="setting" icon="setting">Einstellungen</v-link>
                <v-link @click.prevent="$inertia.post('/logout')" icon="logout" href="/logout">Abmelden</v-link>
            </div>
            <a href="#" @click.prevent="menuOverflowVisible = false" v-if="menuOverflowVisible && !menuVisible" class="absolute right-0 top-0 mr-2 mt-2">
                <svg-sprite src="close" class="w-5 h-5 text-gray-300"></svg-sprite>
            </a>
        </div>

        <div class="grow bg-gray-900 flex flex-col transition-all" :class="{'ml-56': menuVisible, 'ml-0': !menuVisible}">
            <div class="h-16 px-6 flex items-center space-x-3 border-b border-gray-600">
                <a href="#" @click.prevent="menuOverflowVisible = !menuOverflowVisible" class="lg:hidden">
                    <svg-sprite src="menu" class="text-gray-100 w-5 h-5"></svg-sprite>
                </a>
                <span class="text-sm md:text-xl font-semibold text-white leading-none" v-html="$page.props.title"></span>
                <i-link v-for="(link, index) in filterMenu" :key="index" :href="link.href" class="btn label mr-2" :class="`btn-${link.color}`" v-tooltip="tooltipsVisible ? link.label : ''">
                    <svg-sprite v-show="link.icon" class="w-3 h-3 xl:mr-2" :src="link.icon"></svg-sprite>
                    <span class="hidden xl:inline" v-text="link.label"></span>
                </i-link>
                <div class="flex grow justify-between">
                    <portal-target name="toolbar-left"> </portal-target>
                    <portal-target name="toolbar-right"> </portal-target>
                </div>
            </div>

            <div class="grow flex flex-col">
                <slot></slot>
            </div>
        </div>
    </div>
</template>

<script>
import VLink from './_VLink.vue';
import {debounce} from 'lodash';
import mergesQueryString from '../mixins/mergesQueryString.js';

export default {
    data: function () {
        return {
            menuVisible: true,
            menuOverflowVisible: false,
            tooltipsVisible: false,
        };
    },
    components: {
        VNotification: () => import('../components/VNotification.vue'),
        VLink,
    },
    mixins: [mergesQueryString],

    computed: {
        filterMenu() {
            return this.$page.props.toolbar ? this.$page.props.toolbar.filter((menu) => menu.show !== false) : [];
        },
    },

    methods: {
        menuListener() {
            var x = window.matchMedia('(min-width: 1024px)');

            if (x.matches && !this.menuVisible) {
                console.log('A');
                this.menuVisible = true;
                this.menuOverflowVisible = false;
                return;
            }
            if (!x.matches && this.menuVisible) {
                this.menuVisible = false;
                this.menuOverflowVisible = false;
                return;
            }

            this.tooltipsVisible = !window.matchMedia('(min-width: 1280px)').matches;
        },
    },
    created() {
        var _self = this;
        window.addEventListener('resize', this.menuListener);
        this.menuListener();

        window.addEventListener('inertiaStart', () => {
            if (!window.matchMedia('(min-width: 1024px)').matches) {
                _self.menuVisible = false;
                _self.menuOverflowVisible = false;
            }
        });
    },
};
</script>

<style scoped>
.main-grid {
    grid-template-columns: min-content 1fr;
    display: grid;
}
</style>
