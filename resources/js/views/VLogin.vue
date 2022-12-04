<template>
    <form @submit.prevent="submit">
        <div class="h-24 p-6 md:px-10 bg-primary-800 flex justify-between items-center w-full">
            <span class="text-primary-500 text-xl">Login</span>
            <img src="/img/dpsg.gif" class="w-24" />
        </div>
        <div class="p-6 md:p-10 grid gap-5">
            <f-text id="email" label="E-Mail-Adresse" v-model="values.email"></f-text>
            <f-text id="password" type="password" label="Passwort" v-model="values.password"></f-text>
            <button type="submit" class="btn btn-primary">Login</button>
        </div>
    </form>
</template>

<script>
import FullLayout from '../layouts/FullLayout';

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
