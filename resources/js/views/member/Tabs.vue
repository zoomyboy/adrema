<template>
    <section>
        <div class="flex space-x-2 border-b border-teal-200">
            <a
                v-for="(v, index) in inner.children"
                href="#"
                @click.prevent="navigate(index)"
                class="text-teal-800 font-semibold hover:text-teal-600 transition-all"
                :class="{'text-teal-600': inner.active === index}"
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
    data: function () {
        return {
            inner: {
                children: {},
                active: null,
            },
        };
    },

    props: {
        modelValue: {},
    },

    methods: {
        navigate(v) {
            this.inner.active = v;
            this.$emit('update:modelValue', this.inner);
        },
    },

    created() {
        this.inner = this.modelValue;
    },
};
</script>
