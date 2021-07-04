<template>
    <div class="h-16 px-6 flex justify-between items-center border-b border-solid border-gray-500">
        <div class="flex items-center">
            <span class="text-xl font-semibold text-white" v-html="title"></span>
            <a v-for="link in links.filter(link => link.href === undefined)" v-text="link.label" @click.prevent="$emit(link.event)" href="#" class="btn label btn-primary-light icon"></a>
            <a v-for="link in links.filter(link => link.href !== undefined)" v-text="link.label" :href="link.href" class="btn label btn-primary-light ml-1"></a>
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
