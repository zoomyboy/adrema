<template>
    <div class="grid gap-2 has-contents">
        <transition-group name="fadeRight" tag="div">
            <div v-for="(item, index) in items" :key="'i'+index"
                 :class="`bg-${item.type}-300 text-${item.type}-800 h-12 flex flex-col items-start justify-center shadow-2xl rounded-sm px-6`"
            >
                <div class="text-sm" v-for="message in item.messages" v-text="message"></div>
            </div>
        </transition-group>
    </div>
</template>

<script>
export default {
    computed: {
        items() {
            var i = [];

            for (const field in this.$page.props.errors) {
                i.push({ messages: this.$page.props.errors[field], type: 'red' });
            }

            if (this.$page.props.flash && this.$page.props.flash.success) {
                i.push({ messages: [ this.$page.props.flash.success ], type: 'green' });
            }

            return i;

            return this.$page.props.errors;
        }
    }
};
</script>
