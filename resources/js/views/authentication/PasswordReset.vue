<template>
    <page-full-layout banner>
        <template #heading>
            <page-full-heading-banner>Passwort vergessen</page-full-heading-banner>
        </template>
        <form @submit.prevent="submit">
            <div class="grid gap-5">
                <span class="text-gray-500 text-sm"
                    >Hier kannst du dein Passwort zurücksetzen.<br />
                    Gebe dafür die E-Mail-Adresse deines Benutzerkontos ein.<br />
                    Anschließend bekommst du eine E-Mail<br />
                    mit weiteren Anweisungen.</span
                >
                <f-text id="email" v-model="values.email" name="email" label="E-Mail-Adresse"></f-text>
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

    data: function () {
        return {
            values: {
                email: '',
            },
        };
    },
    methods: {
        async submit() {
            await this.axios.post('/password/email', this.values);
            this.$success('Du hast weitere Instruktionen per E-Mail erhalten.');
        },
    },
};
</script>
