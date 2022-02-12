<template>
    <div class="grid gap-2 has-contents">
        <transition-group name="fadeRight" tag="div">
            <div v-for="item, index in items" :key="'i'+index"
                 :class="`${colors[item.type].bg} ${colors[item.type].text} h-12 flex flex-col items-start justify-center shadow-2xl rounded-sm px-6`"
            >
                <div class="text-sm" v-for="message, mindex in item.messages" :key="mindex" v-text="message"></div>
            </div>
        </transition-group>
    </div>
</template>

<script>
export default {
    data: function() {
        return {
            colors: {
                red: {
                    bg: 'bg-red-300',
                    text: 'text-red-800'
                },
                green: {
                    bg: 'bg-green-300',
                    text: 'text-green-800'
                }
            }
        };
    },
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
        }
    }
};
</script>
