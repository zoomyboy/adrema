<template>
    <v-notification class="fixed z-40 right-0 bottom-0 mb-3 mr-3"></v-notification>
    <div class="flex justify-center items-center grow min-h-full">
        <div class="w-64 sm:w-72 md:w-96 bg-gray-800 rounded-xl overflow-hidden shadow-lg">
            <slot></slot>
        </div>
    </div>
</template>

<script>
import VNotification from '../components/VNotification.vue';

export default {
    components: {
        VNotification,
    },
};
</script>
