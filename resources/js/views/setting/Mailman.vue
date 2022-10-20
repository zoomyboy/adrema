<template>
    <form class="grow p-6 grid grid-cols-2 gap-3 items-start content-start" @submit.prevent="submit">
        <f-text label="URL" hint="URL der Mailman Api" name="base_url" id="base_url" v-model="inner.base_url"></f-text>
        <f-text label="Benutzername" name="username" id="username" v-model="inner.username"></f-text>
        <f-text label="Passwort" name="password" id="password" v-model="inner.password"></f-text>
        <div></div>
        <div>
            <button type="submit" class="mt-3 btn block btn-primary">Speichern</button>
        </div>
    </form>
</template>

<script>
import AppLayout from '../../layouts/AppLayout.vue';
import SettingLayout from './Layout.vue';

export default {
    layout: [AppLayout, SettingLayout],

    data: function () {
        return {
            inner: {},
        };
    },
    props: {
        data: {},
    },
    methods: {
        submit() {
            this.$inertia.post('/setting/mailman', this.inner);
        },
    },
    created() {
        this.inner = this.data;
    },
};
</script>
