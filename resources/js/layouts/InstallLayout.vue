<template>
    <div id="app" class="bg-gray-900 font-sans flex flex-col grow items-center justify-center p-6">
        <v-notification class="fixed z-40 right-0 bottom-0 mb-3 mr-3"></v-notification>
        <div class="bg-gray-800 rounded-xl overflow-hidden shadow-lg p-6">
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
