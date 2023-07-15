<template>
    <Head :title="$page.props.title"></Head>
    <v-notification class="fixed z-40 right-0 bottom-0 mb-3 mr-3"></v-notification>
    <div class="flex justify-center items-center grow min-h-full px-6">
        <slot></slot>
    </div>
</template>

<script>
import {defineAsyncComponent} from 'vue';

export default {
    components: {
        VNotification: defineAsyncComponent(() => import('../components/VNotification.vue')),
    },
};
</script>
