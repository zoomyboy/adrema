<template>
    <form action="/contribution/generate" target="_BLANK" class="max-w-2xl w-full mx-auto gap-6 grid-cols-2 grid p-6">
        <f-text
            id="eventName"
            name="eventName"
            class="col-span-2"
            v-model="values.eventName"
            label="Veranstaltungs-Name"
            required
        ></f-text>
        <f-text id="dateFrom" name="dateFrom" type="date" v-model="values.dateFrom" label="Datum von" required></f-text>
        <f-text
            id="dateUntil"
            name="dateUntil"
            type="date"
            v-model="values.dateUntil"
            label="Datum bis"
            required
        ></f-text>
        <button
            target="_BLANK"
            type="submit"
            name="type"
            value="\App\Contribution\SolingenData"
            class="btn btn-primary mt-3 inline-block"
        >
            Für Stadt erstellen
        </button>
    </form>
</template>

<script>
export default {
    data: function () {
        return {
            values: {
                event_name: '',
                dateFrom: '',
                dateUntil: '',
            },
        };
    },
    props: {
        members: {},
    },
};
</script>
