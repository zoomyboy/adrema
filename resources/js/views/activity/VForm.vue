<template>
    <form id="actionform" class="grow p-3" @submit.prevent="submit">
        <f-text id="name" v-model="inner.name" label="Name" required></f-text>
        <checkboxes-label class="mt-6">Untertätigkeiten</checkboxes-label>
        <div class="grid gap-2 sm:grid-cols-2 md:grid-cols-4">
            <f-switch
                inline
                size="sm"
                :key="option.id"
                v-model="inner.subactivities"
                name="subactivities[]"
                :id="`subactivities-${option.id}`"
                :value="option.id"
                :label="option.name"
                v-for="option in subactivities"
            ></f-switch>
        </div>
        <save-button form="actionform"></save-button>

        <new-subactivity @stored="reloadSubactivities" :activity-id="inner.id"></new-subactivity>
    </form>
</template>

<script>
export default {
    data: function () {
        return {
            subactivities: [...this.meta.subactivities],
            inner: {...this.data},
            mode: this.data.name === '' ? 'create' : 'edit',
        };
    },

    props: {
        data: {},
        meta: {},
    },

    components: {
        'checkboxes-label': () => import('../../components/Form/CheckboxesLabel'),
        'new-subactivity': () => import('./NewSubactivity.vue'),
    },

    methods: {
        submit() {
            this.mode === 'create' ? this.$inertia.post('/activity', this.inner) : this.$inertia.patch(`/activity/${this.inner.id}`, this.inner);
        },

        reloadSubactivities(model) {
            var _self = this;

            this.$inertia.reload({
                onSuccess(page) {
                    _self.subactivities = page.props.meta.subactivities;
                    _self.inner.subactivities.push(model.id);
                    _self.$success('Untertätigkeit gespeichert.');
                },
            });
        },
    },
};
</script>
