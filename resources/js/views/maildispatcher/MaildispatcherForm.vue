<template>
    <page-layout>
        <template #toolbar>
            <page-toolbar-button :href="meta.links.index" color="primary" icon="undo">Zurück</page-toolbar-button>
        </template>
        <form id="form" class="p-3 grid gap-3" @submit.prevent="submit">
            <f-save-button form="form"></f-save-button>
            <ui-box heading="Filterregeln">
                <div class="grid gap-4 sm:grid-cols-2">
                    <f-multipleselect
                        id="activity_ids"
                        name="activity_ids"
                        :options="meta.activities"
                        v-model="model.filter.activity_ids"
                        @input="reload(1)"
                        label="Tätigkeit"
                        size="sm"
                        required
                    ></f-multipleselect>
                    <f-multipleselect
                        id="subactivity_ids"
                        name="subactivity_ids"
                        :options="meta.subactivities"
                        v-model="model.filter.subactivity_ids"
                        @input="reload(1)"
                        label="Unterttätigkeit"
                        size="sm"
                        required
                    ></f-multipleselect>
                </div>
            </ui-box>
            <ui-box heading="Mitglieder" v-if="members !== null">
                <table cellspacing="0" cellpadding="0" border="0" class="custom-table custom-table-sm hidden md:table">
                    <thead>
                        <th></th>
                        <th>Nachname</th>
                        <th>Vorname</th>
                        <th>E-Mail-Adresse</th>
                        <th>E-Mail-Adresse Eltern</th>
                    </thead>

                    <tr v-for="(member, index) in members.data" :key="index">
                        <td><ui-age-groups :member="member"></ui-age-groups></td>
                        <td v-text="member.lastname"></td>
                        <td v-text="member.firstname"></td>
                        <td v-text="member.email"></td>
                        <td v-text="member.email_parents"></td>
                    </tr>
                </table>
                <ui-pagination class="mt-4" @reload="reload" :value="members.meta" :only="['data']"></ui-pagination>
            </ui-box>
        </form>
    </page-layout>
</template>

<script>
import indexHelpers from '../../mixins/indexHelpers.js';

export default {
    mixins: [indexHelpers],

    data: function () {
        return {
            model: this.mode === 'create' ? {...this.meta.default_model} : {...this.data},
            members: null,
        };
    },

    props: {
        data: {},
        mode: {},
        meta: {},
    },

    methods: {
        async reload(page) {
            this.members = (
                await this.axios.post('/api/member/search', {
                    page: page || 1,
                    filter: this.toFilterString(this.model.filter),
                })
            ).data;
        },
    },

    async created() {
        this.reload();
    },
};
</script>
