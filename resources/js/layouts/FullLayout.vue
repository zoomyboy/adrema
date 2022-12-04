<template>
    <div id="app" class="bg-gray-900 font-sans flex flex-col grow items-center justify-center">
        <v-notification class="fixed z-40 right-0 bottom-0 mb-3 mr-3"></v-notification>
        <div class="w-64 sm:w-72 md:w-96 bg-gray-800 rounded-xl overflow-hidden shadow-lg">
            <slot></slot>
        </div>
    </div>
</template>

<script>
export default {
    components: {
        VNotification: () => import('../components/VNotification.vue'),
    },
};
</script>
