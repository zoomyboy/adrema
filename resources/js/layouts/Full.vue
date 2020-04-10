<template>
    <div id="app" class="font-sans flex flex-col flex-grow">
        <notification :errors="$page.errors"></notification>
        <fullpage>
            <div slot="brand-icon" slot-scope="{ cls }" :class="cls" class="py-2 flex justify-between items-center w-full">
                <span class="text-primary-200 text-lg">Login über NaMi</span>
                <img src="img/dpsg.gif" class="w-24">
            </div>
            <slot />
        </fullpage>
    </div>
</template>

<script>
import Notification from 'agnoster/components/Notification.vue';
import Fullpage from 'agnoster/components/Fullpage.vue';

export default {
    computed: {
        title() {
            return this.$store.getters.title;
        }
    },
    components: { Notification, Fullpage }
};
</script>
