<template>
    <div>
        <v-table :headers="[
            {text: 'Nachname', value: 'lastname', align: 'left'},
            {text: 'Vorname', value: 'firstname', align: 'left'},
            {text: 'Adresse', value: 'address', align: 'left'},
        ]" :items="data" actions>
            <template slot="Nachname" slot-scope="{item}">
                {{item.lastname}}
            </template>
            <template slot="Vorname" slot-scope="{item}">
                {{item.firstname}}
            </template>
            <template slot="Adresse" slot-scope="{item}">
                {{item.address}} {{item.zip}}, {{item.city}}
            </template>
            <template slot="Aktion" slot-scope="{item}"></template>
        </v-table>
    </div>
</template>

<script>
    import App from '../../layouts/App';

    export default {
        layout: App,

        props: ['data']
    }
</script>

