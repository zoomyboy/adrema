<template>
    <page-full-layout banner>
        <template #heading>
            <page-full-heading-banner>Login</page-full-heading-banner>
        </template>
        <form @submit.prevent="submit">
            <div class="grid gap-5">
                <f-text id="email" v-model="values.email" label="E-Mail-Adresse"></f-text>
                <f-text id="password" v-model="values.password" type="password" label="Passwort"></f-text>
                <button type="submit" class="btn btn-primary">Login</button>
                <div class="flex justify-center">
                    <button type="button" class="text-gray-500 text-sm hover:text-gray-300" @click.prevent="$inertia.visit('/password/reset')">Passwort vergessen?</button>
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
                password: '',
            },
        };
    },
    methods: {
        submit() {
            this.$inertia.post('/login', this.values);
        },
    },
};
</script>
