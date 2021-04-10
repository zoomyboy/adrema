<template>
    <form @submit.prevent="submit">
        <div class="h-24 px-10 bg-primary flex justify-between items-center w-full">
            <span class="text-primary-200 text-lg">Login über NaMi</span>
            <img src="/img/dpsg.gif" class="w-24">
        </div>
        <div class="p-10 grid gap-5">
            <f-text id="mglnr" label="Mitgliedsnummer" v-model="values.mglnr"></f-text>
            <f-text id="password" type="password" label="Passwort" v-model="values.password"></f-text>
            <f-text id="groupid" name="groupid" label="Gruppierungsnummer" v-model="values.groupid"></f-text>
            <button type="submit" class="btn btn-primary">Login</button>
        </div>
    </form>
</template>

<script>
import Full from '../layouts/Full';

export default {
    layout: Full,

    data: function() {
        return {
            values: {}
        };
    },
    methods: {
        submit() {
            this.$inertia.post('/login', this.values);
        }
    }
};
</script>
