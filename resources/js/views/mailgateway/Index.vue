<template>
    <page-layout>
        <template #toolbar>
            <page-toolbar-button @click.prevent="model = {...data.meta.default}" color="primary" icon="plus">Neue Verbindung</page-toolbar-button>
        </template>
        <ui-popup heading="Neue Verbindung" v-if="model !== null && !model.id" @close="model = null">
            <form @submit.prevent="submit">
                <section class="grid grid-cols-2 gap-3 mt-6">
                    <f-text v-model="model.name" name="name" id="name" label="Bezeichnung" required></f-text>
                    <f-text v-model="model.domain" name="domain" id="domain" label="Domain" required></f-text>
                    <f-select
                        :value="model.type.cls"
                        @input="
                            model.type = {
                                cls: $event,
                                params: {...getType($event).defaults},
                            }
                        "
                        label="Typ"
                        name="type"
                        id="type"
                        :options="data.meta.types"
                        :placeholder="''"
                        required
                    ></f-select>
                    <template v-for="(field, index) in getType(model.type.cls).fields">
                        <f-text
                            :key="index"
                            v-if="field.type === 'text'"
                            :label="field.label"
                            :name="field.name"
                            :id="field.name"
                            v-model="model.type.params[field.name]"
                            :required="field.is_required"
                        ></f-text>
                    </template>
                </section>
                <section class="flex mt-4 space-x-2">
                    <ui-button type="submit" class="btn-danger">Speichern</ui-button>
                    <ui-button @click.prevent="model = null" class="btn-primary">Abbrechen</ui-button>
                </section>
            </form>
        </ui-popup>
        <setting-layout>
            <div class="w-full h-full pb-6">
                <table cellspacing="0" cellpadding="0" border="0" class="custom-table custom-table-sm hidden md:table">
                    <thead>
                        <th>Bezeichnung</th>
                        <th>Domain</th>
                        <th>Typ</th>
                        <th>Prüfung</th>
                        <th>Aktion</th>
                    </thead>

                    <tr v-for="(gateway, index) in inner.data" :key="index">
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
                        <td>
                            <a href="#" v-tooltip="`Bearbeiten`" @click.prevent="model = {...gateway}" class="inline-flex btn btn-warning btn-sm"><svg-sprite src="pencil"></svg-sprite></a>
                        </td>
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
import indexHelpers from '../../mixins/indexHelpers.js';

export default {
    mixins: [indexHelpers],

    data: function () {
        return {
            model: null,
            inner: {...this.data},
        };
    },
    props: {
        data: {},
    },

    methods: {
        getType(type) {
            return this.data.meta.types.find((t) => t.id === type);
        },
        async submit() {
            try {
                await this.axios.post(this.data.meta.links.store, this.model);

                this.reload();
                this.model = null;
            } catch (e) {
                this.errorsFromException(e);
            }
        },
    },
    components: {
        SettingLayout,
    },
};
</script>
