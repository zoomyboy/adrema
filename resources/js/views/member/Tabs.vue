<template>
    <section>
        <div class="flex space-x-2 border-b border-teal-200">
            <a
                v-for="(v, index) in inner.children"
                href="#"
                class="font-semibold hover:text-teal-600 transition-all"
                :class="{'text-teal-800': inner.active !== index, 'text-teal-600': inner.active === index}"
                @click.prevent="navigate(index)"
            >
                <span v-text="v"></span>
            </a>
        </div>
        <div class="mt-3">
            <slot></slot>
        </div>
    </section>
</template>

<script>
export default {

    props: {
        modelValue: {},
    },
    data: function () {
        return {
            inner: {
                children: {},
                active: null,
            },
        };
    },

    created() {
        this.inner = this.modelValue;
    },

    methods: {
        navigate(v) {
            this.inner.active = v;
            this.$emit('update:modelValue', this.inner);
        },
    },
};
</script>
