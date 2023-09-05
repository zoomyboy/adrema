<template>
    <div class="sidebar flex flex-col group is-bright">
        <page-header title="Mitgliedschaften" @close="$emit('close')">
            <template #toolbar>
                <page-toolbar-button v-if="single === null" color="primary" icon="plus" @click.prevent="create">Neue
                    Mitgliedschaft</page-toolbar-button>
                <page-toolbar-button v-if="single !== null" color="primary" icon="undo"
                    @click.prevent="cancel">Zurück</page-toolbar-button>
            </template>
        </page-header>

        <form v-if="single" class="p-6 grid gap-4 justify-start" @submit.prevent="submit">
            <f-select id="group_id" v-model="single.group_id" name="group_id" :options="groups" label="Gruppierung"
                required></f-select>
            <f-select id="activity_id" v-model="single.activity_id" name="activity_id" :options="activities"
                label="Tätigkeit" required></f-select>
            <f-select v-if="single.activity_id" id="subactivity_id" v-model="single.subactivity_id" name="subactivity_id"
                :options="subactivities[single.activity_id]" label="Untertätigkeit" size="sm"></f-select>
            <f-switch id="has_promise" :model-value="single.promised_at !== null" label="Hat Versprechen"
                @update:modelValue="single.promised_at = $event ? '2000-02-02' : null"></f-switch>
            <f-text v-show="single.promised_at !== null" id="promised_at" v-model="single.promised_at" type="date"
                label="Versprechensdatum" size="sm"></f-text>
            <button type="submit" class="btn btn-primary">Absenden</button>
        </form>

        <div v-else class="grow">
            <table class="custom-table custom-table-light custom-table-sm text-sm">
                <thead>
                    <th>Tätigkeit</th>
                    <th>Untertätigkeit</th>
                    <th>Datum</th>
                    <th>Aktiv</th>
                    <th></th>
                </thead>

                <tr v-for="(membership, index) in value.memberships" :key="index">
                    <td v-text="membership.activity_name"></td>
                    <td v-text="membership.subactivity_name"></td>
                    <td v-text="membership.human_date"></td>
                    <td><ui-boolean-display :value="membership.is_active" dark></ui-boolean-display></td>
                    <td class="flex">
                        <a href="#" class="inline-flex btn btn-warning btn-sm" @click.prevent="
                            single = membership;
                        mode = 'edit';
                                                        "><ui-sprite src="pencil"></ui-sprite></a>
                        <i-link href="#" class="inline-flex btn btn-danger btn-sm"
                            @click.prevent="remove(membership)"><ui-sprite src="trash"></ui-sprite></i-link>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        value: {},
        activities: {},
        subactivities: {},
        groups: {},
    },
    data: function () {
        return {
            mode: null,
            single: null,
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
            this.single = { ...this.def };
        },
        cancel() {
            this.mode = this.single = null;
        },
        remove(membership) {
            this.$inertia.delete(`/member/${this.value.id}/membership/${membership.id}`);
        },

        accept(payment) {
            this.$inertia.patch(`/member/${this.value.id}/payment/${payment.id}`, { ...payment, status_id: 3 });
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
};
</script>
