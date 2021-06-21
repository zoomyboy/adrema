<template>
    <div id="app" class="flex font-sans flex-grow">

        <!-- ******************************** Sidebar ******************************** -->
        <div class="fixed bg-gray-800 p-6 w-56 left-0 top-0 h-screen border-r border-gray-600 border-solid">
            <div class="grid gap-2">
                <v-link href="/" menu="dashboard" icon="loss">Dashboard</v-link>
                <v-link href="/member" menu="member" icon="user">Mitglieder</v-link>
            </div>
        </div>

        <div class="flex-grow ml-56 bg-gray-900 flex flex-col">
            <div class="h-16 p-6 flex justify-between items-center border-b border-gray-600">
                <div class="flex">
                    <span class="text-xl font-semibold text-white" v-html="$page.props.title"></span>
                    <div class="flex ml-4">
                        <inertia-link v-for="link, index in $page.props.toolbar" :key="index" :href="link.href" v-text="link.label" class="rounded-full leading-none px-3 py-2 text-sm" :class="`bg-${link.color}-800 text-${link.color}-500 hover:text-${link.color}-400 hover:bg-${link.color}-700 transition-all transition-300`">
                            <sprite :src="link.icon"></sprite>
                        </inertia-link>
                    </div>
                </div>
                <label for="search">
                    <input class="shadow-lg bg-gray-800 rounded-lg py-2 px-3 text-gray-300 hover:bg-gray-700 focus:bg-gray-700 placeholder-gray-400" placeholder="Suchen…" name="search" v-model="isearch"></input>
                </label>
            </div>

            <div class="flex-grow flex flex-col">
                <slot></slot>
            </div>
        </div>

    </div>
</template>

<script>
import VLink from './_VLink.vue';
import { debounce } from 'lodash';
import mergesQueryString from '../mixins/mergesQueryString.js';

export default {
    components: { VLink },
    mixins: [ mergesQueryString ],

    computed: {
        isearch: {
            set: debounce(function(v) {
                this.$inertia.visit(this.qs({ search: v }), {
                    only: ['search', 'data'],
                    preserveState: true,
                });
            }, 500),
            get() {
                return this.$page.props.search;
            }
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
