<template>
    <page-full-layout banner>
        <template #heading>
            <page-full-heading-banner>Passwort vergessen</page-full-heading-banner>
        </template>
        <form @submit.prevent="submit">
            <div class="grid gap-5">
                <span class="text-gray-500 text-sm"
                    >Hier kannst du dein Passwort zurücksetzen.<br />
                    Gebe dafür ein neues Passwort ein.<br />
                    Merke oder notiere dir dieses Passwort, bevor du das Formular absendest.<br />
                    Danach wirst du zum Dashboard weitergeleitet.</span
                >
                <f-text id="password" v-model="values.password" type="password" name="password" label="Neues Passwort"></f-text>
                <f-text id="password_confirmation" v-model="values.password_confirmation" type="password" name="password_confirmation" label="Neues Passwort widerholen"></f-text>
                <button type="submit" class="btn btn-primary">Passwort zurücksetzen</button>
                <div class="flex justify-center">
                    <button type="button" class="text-gray-500 text-sm hover:text-gray-300" @click.prevent="$inertia.visit('/login')">Zurück zum Login</button>
                </div>
            </div>
        </form>
    </page-full-layout>
</template>

<script>
import FullLayout from '../../layouts/FullLayout.vue';

export default {
    layout: FullLayout,

    props: {
        token: {
            type: String,
            required: true,
        },
        email: {
            type: String,
            required: true,
        },
    },

    data: function () {
        return {
            values: {
                password: '',
                password_confirmation: '',
            },
        };
    },
    methods: {
        async submit() {
            await this.axios.post('/password/reset', {
                ...this.values,
                email: this.email,
                token: this.token,
            });
            this.$success('Dein Passwort wurde zurückgesetzt.');
            this.$inertia.visit('/');
        },
    },
};
</script>
