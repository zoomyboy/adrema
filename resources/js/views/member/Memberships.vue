<template>
    <div class="sidebar flex flex-col">
        <sidebar-header :links="links" @create="mode = 'create'; single = {}" @close="$emit('close')" title="Mitgliedschaften"></sidebar-header>

        <form v-if="single" class="p-6 grid gap-4 justify-start" @submit.prevent="submit">
            <f-select id="activity_id" :options="activities" v-model="single.activity_id" label="Tätigkeit" required></f-select>
            <f-select v-if="single.activity_id" :options="subactivities[single.activity_id]" id="subactivity_id" v-model="single.subactivity_id" label="Untertätigkeit"></f-select>
            <button type="submit" class="btn btn-primary">Absenden</button>
        </form>

        <table v-else class="custom-table custom-table-light custom-table-sm text-sm flex-grow">
            <thead>
                <th>Tätigkeit</th>
                <th>Untertätigkeit</th>
                <th>Datum</th>
                <th></th>
            </thead>

            <tr v-for="membership, index in value.memberships">
                <td v-text="membership.activity_name"></td>
                <td v-text="membership.subactivity_name"></td>
                <td v-text="membership.human_date"></td>
                <td class="flex">
                    <a href="#" @click.prevent="single = membership; mode = 'edit'" class="inline-flex btn btn-warning btn-sm"><sprite src="pencil"></sprite></a>
                    <inertia-link href="#" @click.prevent="remove(membership)" class="inline-flex btn btn-danger btn-sm"><sprite src="trash"></sprite></inertia-link>
                </td>
            </tr>
        </table>
    </div>
</template>

<script>
import SidebarHeader from '../../components/SidebarHeader.vue';

export default {
    data: function() {
        return {
            mode: null,
            single: null,
            links: [
                { event: 'create', label: 'Neu' }
            ]
        };
    },

    components: { SidebarHeader },

    methods: {
        remove(payment) {
            this.$inertia.delete(`/member/${this.value.id}/payment/${payment.id}`);
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
                onFinish() {
                    _self.single = null;
                    _self.mode = null;
                }
            };

            this.mode === 'create' 
                ? this.$inertia.post(`/member/${this.value.id}/membership`, this.single, options)
                : this.$inertia.patch(`/member/${this.value.id}/membership/${this.single.id}`, this.single, options);
        }
    },

    props: {
        value: {},
        activities: {},
        subactivities: {},
    }
};
</script>
