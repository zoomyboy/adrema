<template>
    <div id="app" class="flex font-sans grow">
        <v-notification class="fixed z-40 right-0 bottom-0 mb-3 mr-3"></v-notification>

        <!-- ******************************** Sidebar ******************************** -->
        <div
            class="fixed z-40 bg-gray-800 p-6 w-56 top-0 h-screen border-r border-gray-600 border-solid flex flex-col justify-between transition-all"
            :class="{
                '-left-[14rem]': !menuStore.isShifted,
                'left-0': menuStore.isShifted,
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
            <a href="#" @click.prevent="menuStore.hide()" v-if="menuStore.hideable" class="absolute right-0 top-0 mr-2 mt-2">
                <svg-sprite src="close" class="w-5 h-5 text-gray-300"></svg-sprite>
            </a>
        </div>

        <slot></slot>
    </div>
</template>

<script>
import VLink from './_VLink.vue';
import {menuStore} from '../stores/menuStore.js';

export default {
    data: function () {
        return {
            menuStore: menuStore(),
        };
    },
    components: {
        VNotification: () => import('../components/VNotification.vue'),
        VLink,
    },

    computed: {
        filterMenu() {
            return this.$page.props.toolbar ? this.$page.props.toolbar.filter((menu) => menu.show !== false) : [];
        },
    },

    created() {
        this.menuStore.startInertiaListener();
    },
};
</script>

<style scoped>
.main-grid {
    grid-template-columns: min-content 1fr;
    display: grid;
}
</style>
