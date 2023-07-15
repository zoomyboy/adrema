<template>
    <page-full-layout banner>
        <template #heading>
            <page-full-heading-banner>Login</page-full-heading-banner>
        </template>
        <form @submit.prevent="submit">
            <div class="grid gap-5">
                <f-text id="email" name="email" label="E-Mail-Adresse" v-model="values.email"></f-text>
                <f-text id="password" name="password" type="password" label="Passwort" v-model="values.password"></f-text>
                <button type="submit" class="btn btn-primary">Login</button>
            </div>
        </form>
    </page-full-layout>
</template>

<script>
import FullLayout from '../layouts/FullLayout.vue';

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
