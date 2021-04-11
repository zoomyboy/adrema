<template>
    <div class="flex font-sans flex-grow">

        <!-- ******************************** Sidebar ******************************** -->
        <div class="fixed bg-gray-800 p-6 w-56 left-0 top-0 h-screen border-r border-gray-600 border-solid">
            <div class="grid gap-2">
                <v-link href="/" menu="dashboard" icon="loss">Dashboard</v-link>
                <v-link href="/member" menu="member" icon="user">Mitglieder</v-link>
            </div>
        </div>

        <div class="flex-grow ml-56 bg-gray-900 flex flex-col">
            <div class="h-16 text-xl font-semibold text-white flex items-center pl-5 border-b border-gray-600" v-html="$page.props.title"></div>

            <div class="flex-grow">
                <slot></slot>
            </div>
        </div>

    </div>
</template>

<script>
import VLink from './_VLink.vue';

export default {
    components: { VLink }
};
</script>

<style scoped>
.main-grid {
    grid-template-columns: min-content 1fr;
    display: grid;
}
</style>
