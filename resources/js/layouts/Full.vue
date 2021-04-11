<template>
    <div id="app" class="bg-gray-900 font-sans flex flex-col flex-grow items-center justify-center">
        <div class="w-96 bg-gray-800 rounded-xl overflow-hidden shadow-lg">
            <slot></slot>
        </div>
    </div>
</template>

<script>
export default {
    computed: {
    }
};
</script>
