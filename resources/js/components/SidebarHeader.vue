<template>
    <div class="h-16 px-6 flex justify-between items-center border-b border-solid border-gray-500">
        <div class="flex items-center">
            <span class="mr-1 text-xl font-semibold leading-none text-white" v-html="title"></span>
            <a v-for="link in links.filter(link => link.icon === undefined)" @click.prevent="$emit(link.event)" href="#" class="btn label btn-primary-light">
                <span v-if="link.label" v-text="link.label"></span>
                <sprite v-if="link.icon" :src="link.icon"></sprite>
            </a>
            <a v-for="link in links.filter(link => link.icon !== undefined)" :href="link.href" class="btn label icon btn-primary-light ml-1">
                <span v-if="link.label" v-text="link.label"></span>
                <sprite v-if="link.icon" :src="link.icon"></sprite>
            </a>
        </div>
        <div class="flex ml-4">
            <a href="#" @click.prevent="$emit('close')" class="btn label btn-primary-light icon">
                <sprite class="w-3 h-3" src="close"></sprite>
            </a>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        links: {
            default: function() { return []; }
        },
        title: {
            default: function() { return ''; }
        }
    }
};
</script>
