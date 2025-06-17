<template>
    <page-layout>
        <template #toolbar>
            <page-toolbar-button :href="data.meta.links.create" color="primary" icon="plus">Verteiler erstellen</page-toolbar-button>
        </template>
        <ui-popup v-if="deleting !== null" heading="Verteiler löschen?" @close="deleting.reject()">
            <div>
                <p class="mt-4">Den Verteiler "{{ deleting.dispatcher.name }}" löschen?</p>
                <div class="grid grid-cols-2 gap-3 mt-6">
                    <a href="#" class="text-center btn btn-danger" @click.prevent="deleting.resolve()">Löschen</a>
                    <a href="#" class="text-center btn btn-primary" @click.prevent="deleting.reject()">Abbrechen</a>
                </div>
            </div>
        </ui-popup>
        <table cellspacing="0" cellpadding="0" border="0" class="custom-table custom-table-sm">
            <thead>
                <th>Name</th>
                <th>Domain</th>
                <th>Verbindung</th>
                <th />
            </thead>

            <tr v-for="(dispatcher, index) in data.data" :key="index">
                <td>
                    <div v-text="dispatcher.name" />
                </td>
                <td>
                    <div v-text="dispatcher.gateway.domain" />
                </td>
                <td>
                    <div v-text="dispatcher.gateway.name" />
                </td>
                <td>
                    <i-link :href="dispatcher.links.edit" class="mr-1 inline-flex btn btn-warning btn-sm"><ui-sprite src="pencil" /></i-link>
                    <button class="inline-flex btn btn-danger btn-sm" @click.prevent="remove(dispatcher)"><ui-sprite src="trash" /></button>
                </td>
            </tr>
        </table>
    </page-layout>
</template>

<script>
export default {
    props: {
        data: {},
    },
    data: function () {
        return {
            deleting: null,
        };
    },
    methods: {
        async remove(dispatcher) {
            new Promise((resolve, reject) => {
                this.deleting = {resolve, reject, dispatcher};
            })
                .then(() => {
                    this.$inertia.delete(dispatcher.links.delete);
                    this.deleting = null;
                })
                .catch(() => {
                    this.deleting = null;
                });
        },
    },
};
</script>
