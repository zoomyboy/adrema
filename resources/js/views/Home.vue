<template>
    <div class="gap-6 grid-cols-4 grid p-6">
        <inertia-link v-for="block, index in blocks" class="items-center text-gray-800 bg-gray-100 hover:bg-gray-300 p-6 shadow flex flex-col" :href="block.href" :key="index">
            <span class="flex-grow mb-4">
                <span :class="block.icon" class="fa fa-lg text-gray-700"></span>
            </span>
            <span v-html="block.label"></span>
        </inertia-link>
    </div>
</template>

<script>
import App from '../layouts/App';

export default {
    layout: App,

    props: {
        blocks: {}
    }

};
</script>
