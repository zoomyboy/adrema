<template>
    <div class="grid grid-cols-2">
        <div class="p-6" v-for="type in types">
            <p class="text-white" v-for="paragraph in type.text" v-text="paragraph"></p>
            <a :href="type.link.href" target="_BLANK" class="btn btn-primary mt-3 inline-block" v-text="type.link.label"></a>
        </div>
    </div>
</template>

<script>
export default {
    data: function() {
        return {
            inner: {},
        };
    },

    props: {
        types: {}
    },

    methods: {
        submit() {
            this.$inertia.post(`/allpayment`, this.inner)
        }
    }

};
</script>
