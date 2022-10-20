<template>
    <form class="grow p-6 grid grid-cols-2 gap-3 items-start content-start" @submit.prevent="submit">
        <div class="col-span-full text-gray-100 mb-3">
            <p class="text-sm">
                Scoutrobot kann automatisch Mailinglisten erstellen, wenn es mit einem existierenden
                <a href="https://docs.mailman3.org/en/latest/">Mailman Server</a> verbunden wird. Mailman ist ein
                OpenSource-Mailinglisten-System, um E-Mails an mehrere Leute zu senden.
            </p>
            <p class="text-sm mt-1">
                Scoutrobot wird nach der Ersteinrichtung deine Mitglieder zu bestehenden E-Mail-Verteilern hinzufügen.
            </p>
        </div>
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
