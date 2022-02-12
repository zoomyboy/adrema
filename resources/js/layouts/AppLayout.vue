<template>
    <div id="app" class="flex font-sans grow">

        <notification class="fixed z-40 right-0 bottom-0 mb-3 mr-3"></notification>

        <!-- ******************************** Sidebar ******************************** -->
        <div class="fixed bg-gray-800 p-6 w-56 left-0 top-0 h-screen border-r border-gray-600 border-solid">
            <div class="grid gap-2">
                <v-link href="/" menu="dashboard" icon="loss">Dashboard</v-link>
                <v-link href="/member" menu="member" icon="user">Mitglieder</v-link>
                <v-link href="/subscription" v-show="hasModule('bill')" menu="subscription" icon="money">Beiträge</v-link>
            </div>
        </div>

        <div class="grow ml-56 bg-gray-900 flex flex-col">
            <div class="h-16 px-6 flex justify-between items-center border-b border-gray-600">
                <div class="flex">
                    <span class="text-xl font-semibold text-white leading-none" v-html="$page.props.title"></span>
                    <div class="flex ml-4">
                        <Link v-for="link, index in filterMenu" :key="index" :href="link.href" v-text="link.label" class="btn label mr-2" :class="`btn-${link.color}`">
                            <sprite :src="link.icon"></sprite>
                        </Link>
                    </div>
                </div>
                <label for="search">
                    <input class="shadow-lg bg-gray-800 rounded-lg py-2 px-3 text-gray-300 hover:bg-gray-700 focus:bg-gray-700 placeholder-gray-400" placeholder="Suchen…" name="search" v-model="isearch"></input>
                </label>
            </div>

            <div class="grow flex flex-col">
                <slot></slot>
            </div>
        </div>

    </div>
</template>

<script>
import VLink from './_VLink.vue';
import { debounce } from 'lodash';
import mergesQueryString from '../mixins/mergesQueryString.js';
import Notification from '../components/Notification.vue';

export default {
    components: { Notification, VLink },
    mixins: [ mergesQueryString ],

    computed: {
        isearch: {
            set: debounce(function(v) {
                this.$inertia.visit(this.qs({ page: 1, search: v }), {
                    only: ['page', 'search', 'data'],
                    preserveState: true,
                });
            }, 500),
            get() {
                return this.$page.props.search;
            }
        },
        filterMenu() {
            return this.$page.props.toolbar
                ? this.$page.props.toolbar.filter(menu => menu.show !== false):
                [];
        }
    }

};
</script>

<style scoped>
.main-grid {
    grid-template-columns: min-content 1fr;
    display: grid;
}
</style>
