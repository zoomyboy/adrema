<template>
    <page-layout>
        <div class="p-6 grid h-full items-center justify-center">
            <div class="rounded border-primary-700 border border-solid p-6">
                <div class="text-lg text-gray-200 text-sm">
                    Willkommen bei ScoutRobot.<br />
                    Bitte gib deine Zugangsdaten ein,<br />
                    um eine erste Synchronisation durchzuführen.
                </div>
                <form @submit.prevent="submit" class="grid gap-3 mt-5">
                    <f-text v-model="values.mglnr" label="Mitgliedsnummer" name="mglnr" id="mglnr"></f-text>
                    <f-text v-model="values.password" type="password" label="Passwort" name="password" id="password"></f-text>
                    <f-text v-model="values.group_id" label="Gruppierungsnummer" name="group_id" id="group_id"></f-text>
                    <button type="submit" class="btn w-full btn-primary mt-6 inline-block">Jetzt initialisieren</button>
                </form>
            </div>
        </div>
    </page-layout>
</template>

<script>
export default {
    data: function () {
        return {
            values: {
                mglnr: '',
                password: '',
                group_id: '',
            },
        };
    },
    methods: {
        submit() {
            this.$inertia.post('/initialize', this.values);
        },
    },
};
</script>
