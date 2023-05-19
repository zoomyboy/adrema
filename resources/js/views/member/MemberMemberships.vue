<template>
    <div class="sidebar flex flex-col group is-bright">
        <page-header :links="links" @close="$emit('close')" title="Mitgliedschaften">
            <div class="flex" slot="toolbar">
                <page-toolbar-button @click.prevent="create" color="primary" icon="plus" v-if="single === null">Neue Mitgliedschaft</page-toolbar-button>
                <page-toolbar-button @click.prevent="cancel" color="primary" icon="undo" v-if="single !== null">Zurück</page-toolbar-button>
            </div>
        </page-header>

        <form v-if="single" class="p-6 grid gap-4 justify-start" @submit.prevent="submit">
            <f-select id="group_id" name="group_id" :options="groups" v-model="single.group_id" label="Gruppierung" size="sm" required></f-select>
            <f-select id="activity_id" name="activity_id" :options="activities" v-model="single.activity_id" label="Tätigkeit" size="sm" required></f-select>
            <f-select
                v-if="single.activity_id"
                name="subactivity_id"
                :options="subactivities[single.activity_id]"
                id="subactivity_id"
                v-model="single.subactivity_id"
                label="Untertätigkeit"
                size="sm"
            ></f-select>
            <f-switch id="has_promise" :items="single.promised_at !== null" @input="single.promised_at = $event ? '2000-02-02' : null" size="sm" label="Hat Versprechen"></f-switch>
            <f-text v-show="single.promised_at !== null" type="date" id="promised_at" v-model="single.promised_at" label="Versprechensdatum" size="sm"></f-text>
            <button type="submit" class="btn btn-primary">Absenden</button>
        </form>

        <div class="grow" v-else>
            <table class="custom-table custom-table-light custom-table-sm text-sm">
                <thead>
                    <th>Tätigkeit</th>
                    <th>Untertätigkeit</th>
                    <th>Datum</th>
                    <th></th>
                </thead>

                <tr v-for="(membership, index) in value.memberships" :key="index">
                    <td v-text="membership.activity_name"></td>
                    <td v-text="membership.subactivity_name"></td>
                    <td v-text="membership.human_date"></td>
                    <td class="flex">
                        <a
                            href="#"
                            @click.prevent="
                                single = membership;
                                mode = 'edit';
                            "
                            class="inline-flex btn btn-warning btn-sm"
                            ><svg-sprite src="pencil"></svg-sprite
                        ></a>
                        <i-link href="#" @click.prevent="remove(membership)" class="inline-flex btn btn-danger btn-sm"><svg-sprite src="trash"></svg-sprite></i-link>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</template>

<script>
export default {
    data: function () {
        return {
            mode: null,
            single: null,
            links: [{event: 'create', label: 'Neu'}],
        };
    },

    computed: {
        def() {
            return {
                group_id: this.value.group_id,
                activity_id: null,
                subactivity_id: null,
                promised_at: null,
            };
        },
    },

    methods: {
        create() {
            this.mode = 'create';
            this.single = {...this.def};
        },
        cancel() {
            this.mode = this.single = null;
        },
        remove(membership) {
            this.$inertia.delete(`/member/${this.value.id}/membership/${membership.id}`);
        },

        accept(payment) {
            this.$inertia.patch(`/member/${this.value.id}/payment/${payment.id}`, {...payment, status_id: 3});
        },

        openLink(link) {
            if (link.disabled) {
                return;
            }

            window.open(link.href);
        },

        submit() {
            var _self = this;

            var options = {
                onSuccess() {
                    _self.single = null;
                    _self.mode = null;
                },
            };

            this.mode === 'create'
                ? this.$inertia.post(`/member/${this.value.id}/membership`, this.single, options)
                : this.$inertia.patch(`/member/${this.value.id}/membership/${this.single.id}`, this.single, options);
        },
    },

    props: {
        value: {},
        activities: {},
        subactivities: {},
        groups: {},
    },
};
</script>
