<template>
    <div id="app" class="font-sans flex flex-col flex-grow">
        <notification :errors="$page.errors"></notification>
        <process></process>
        <wrapper
            admin-toolbar="adminToolbar"
            profile-toolbar="profileToolbar"
        >
            <template slot="mainnav">
                <mainnav name="main" sm></mainnav>
            </template>

            <template slot="main">
                <slot />
            </template>
        </wrapper>
    </div>
</template>

<script>
import Notification from 'agnoster/components/Notification.vue';
import Process from 'agnoster/components/Process.vue';
import Wrapper from 'agnoster/components/Wrapper.vue';
import Mainnav from 'agnoster/components/Mainnav.vue';

export default {
    computed: {
        title() {
            return this.$store.getters.title;
        }
    },
    components: { Notification, Wrapper, Mainnav, Process }
};
</script>
