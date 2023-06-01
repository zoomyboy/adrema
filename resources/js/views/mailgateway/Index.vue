<template>
    <page-layout>
        <template #toolbar>
            <page-toolbar-button @click.prevent="popup = true" color="primary" icon="plus">Neue Verbindung</page-toolbar-button>
        </template>
        <ui-popup heading="Neue Verbindung" v-if="popup === true" @close="popup = false">
            <div>
                <div class="grid grid-cols-2 gap-3 mt-6">
                    <a href="#" @click.prevent="submit" class="text-center btn btn-danger">Speichern</a>
                    <a
                        href="#"
                        @click.prevent="
                            value = {};
                            popup = false;
                        "
                        class="text-center btn btn-primary"
                        >Abbrechen</a
                    >
                </div>
            </div>
        </ui-popup>
        <setting-layout>
            <div class="w-full h-full pb-6">
                <table cellspacing="0" cellpadding="0" border="0" class="custom-table custom-table-sm hidden md:table">
                    <thead>
                        <th>Name</th>
                        <th>Domain</th>
                        <th>Typ</th>
                        <th>Prüfung</th>
                        <th>Aktion</th>
                    </thead>

                    <tr v-for="(gateway, index) in inner" :key="index">
                        <td v-text="gateway.name"></td>
                        <td v-text="gateway.domain"></td>
                        <td v-text="gateway.type_human"></td>
                        <td>
                            <ui-boolean-display
                                :value="gateway.works"
                                long-label="Verbindungsstatus"
                                :label="gateway.works ? 'Verbindung erfolgreich' : 'Verbindung fehlgeschlagen'"
                            ></ui-boolean-display>
                        </td>
                        <td></td>
                    </tr>
                </table>

                <div class="px-6">
                    <ui-pagination class="mt-4" :value="data.meta" :only="['data']"></ui-pagination>
                </div>
            </div>
        </setting-layout>
    </page-layout>
</template>

<script>
import SettingLayout from '../setting/Layout.vue';

export default {
    data: function () {
        return {
            popup: false,
            inner: [...this.data.data],
            meta: {...this.data.meta},
        };
    },
    props: {
        data: {},
    },
    components: {
        SettingLayout,
    },
};
</script>
