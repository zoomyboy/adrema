<template>
    <div id="app" class="bg-gray-900 font-sans flex flex-col grow items-center justify-center">
        <notification class="fixed z-40 right-0 bottom-0 mb-3 mr-3"></notification>
        <div class="w-96 bg-gray-800 rounded-xl overflow-hidden shadow-lg">
            <slot></slot>
        </div>
    </div>
</template>

<script>
import Notification from '../components/Notification.vue';

export default {
    components: { Notification },
};
</script>
