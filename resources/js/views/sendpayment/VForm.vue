<template>
    <div class="grid grid-cols-2">
        <div class="p-6" v-for="type, index in types" :key="index">
            <p class="text-white" v-for="paragraph, index in type.text" :key="index" v-text="paragraph"></p>
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
