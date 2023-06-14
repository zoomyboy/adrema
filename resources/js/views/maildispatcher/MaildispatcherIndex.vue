<template>
    <page-layout>
        <template #toolbar>
            <page-toolbar-button :href="data.meta.links.create" color="primary" icon="plus">Verteiler erstellen</page-toolbar-button>
        </template>
        <ui-popup heading="Verteiler löschen?" v-if="deleting !== null" @close="deleting.reject()">
            <div>
                <p class="mt-4">Den Verteiler "{{ deleting.dispatcher.name }}" löschen?</p>
                <div class="grid grid-cols-2 gap-3 mt-6">
                    <a href="#" @click.prevent="deleting.resolve()" class="text-center btn btn-danger">Löschen</a>
                    <a href="#" @click.prevent="deleting.reject()" class="text-center btn btn-primary">Abbrechen</a>
                </div>
            </div>
        </ui-popup>
        <table cellspacing="0" cellpadding="0" border="0" class="custom-table custom-table-sm">
            <thead>
                <th>Name</th>
                <th>Domain</th>
                <th>Verbindung</th>
                <th></th>
            </thead>

            <tr v-for="(dispatcher, index) in data.data" :key="index">
                <td>
                    <div v-text="dispatcher.name"></div>
                </td>
                <td>
                    <div v-text="dispatcher.gateway.domain"></div>
                </td>
                <td>
                    <div v-text="dispatcher.gateway.name"></div>
                </td>
                <td>
                    <i-link :href="dispatcher.links.edit" class="mr-1 inline-flex btn btn-warning btn-sm"><svg-sprite src="pencil"></svg-sprite></i-link>
                    <i-link @click.prevent="remove(dispatcher)" class="inline-flex btn btn-danger btn-sm"><svg-sprite src="trash"></svg-sprite></i-link>
                </td>
            </tr>
        </table>
    </page-layout>
</template>

<script>
export default {
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
    props: {
        data: {},
    },
};
</script>
