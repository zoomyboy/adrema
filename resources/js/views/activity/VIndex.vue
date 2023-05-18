<template>
    <page-layout page-class="pb-6">
        <div class="flex" slot="toolbar">
            <toolbar-button :href="data.meta.links.create" color="primary" icon="plus">Tätigkeit erstellen</toolbar-button>
        </div>
        <popup heading="Bitte bestätigen" v-if="deleting !== null">
            <div>
                <p class="mt-4">Diese Aktivität löschen?</p>
                <div class="grid grid-cols-2 gap-3 mt-6">
                    <a href="#" @click.prevent="remove" class="text-center btn btn-danger">Löschen</a>
                    <a href="#" @click.prevent="deleting = null" class="text-center btn btn-primary">Abbrechen</a>
                </div>
            </div>
        </popup>
        <table cellspacing="0" cellpadding="0" border="0" class="custom-table custom-table-sm table">
            <thead>
                <th>Name</th>
                <th></th>
            </thead>

            <tr v-for="(activity, index) in inner.data" :key="index">
                <td v-text="activity.name"></td>
                <td>
                    <div class="flex space-x-1">
                        <i-link :href="activity.links.edit" class="inline-flex btn btn-warning btn-sm" v-tooltip="`bearbeiten`"><svg-sprite src="pencil"></svg-sprite></i-link>
                        <i-link href="#" @click.prevent="deleting = activity" class="inline-flex btn btn-danger btn-sm" v-tooltip="`Entfernen`"><svg-sprite src="trash"></svg-sprite></i-link>
                    </div>
                </td>
            </tr>
        </table>

        <div class="px-6">
            <v-pages class="mt-4" :value="data.meta" :only="['data']"></v-pages>
        </div>
    </page-layout>
</template>

<script>
import indexHelpers from '../../mixins/indexHelpers';

export default {
    data: function () {
        return {
            deleting: null,
        };
    },

    methods: {
        remove() {
            var _self = this;
            this.$inertia.delete(this.deleting.links.destroy, {
                preserveState: true,
                onSuccess(page) {
                    _self.inner = page.props.data;
                    _self.deleting = null;
                },
            });
        },
    },

    components: {
        popup: () => import(/* webpackChunkName: "ui" */ '../../components/ui/Popup.vue'),
    },

    mixins: [indexHelpers],
};
</script>
