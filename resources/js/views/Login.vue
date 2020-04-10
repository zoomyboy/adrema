<template>
    <form @submit.prevent="submit">
        <grid fullsize>
            <item>
                <f-text label="Mitgliedsnummer" v-model="values.mglnr"></f-text>
            </item>

            <item>
                <f-password label="Passwort" v-model="values.password"></f-password>
            </item>

            <item>
                <f-text label="Gruppierungsnummer" v-model="values.groupid"></f-text>
            </item>

            <item>
                <f-submit data-cy="submit" primary>Login</f-submit>
            </item>
        </grid>
    </form>
</template>

<script>
import Full from '../layouts/Full';

export default {
    layout: Full,

    data: function() {
        return {
            values: {
                mglnr: 90166,
                password: 'ibm0g',
                groupid: 100105
            }
        };
    },
    methods: {
        submit() {
            this.$inertia.post('/login', this.values);
        }
    }
};
</script>
