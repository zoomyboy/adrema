<template>
    <div id="app" class="bg-gray-200 font-sans flex flex-col flex-grow items-center justify-center">
        <div class="w-96 bg-white rounded-xl overflow-hidden shadow-lg">
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
