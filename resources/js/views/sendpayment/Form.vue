<template>
    <div class="p-6">
        <a :href="link.href" target="_BLANK" v-for="link in links" class="btn btn-primary" v-text="link.label"></a>
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
        links: {}
    },

    methods: {
        submit() {
            this.$inertia.post(`/allpayment`, this.inner)
        }
    }

};
</script>
