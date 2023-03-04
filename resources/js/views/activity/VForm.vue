<template>
    <form id="actionform" class="grow p-3" @submit.prevent="submit">
        <popup heading="Neue Untertätigkeit" v-if="mode === 'edit' && addingSubactivity === true" @close="addingSubactivity = false">
            <subactivity-form class="mt-4" :value="inner.subactivity_model" @stored="reloadSubactivities"></subactivity-form>
        </popup>
        <div class="flex space-x-3">
            <f-text id="name" v-model="inner.name" label="Name" required></f-text>
            <f-switch v-model="inner.is_filterable" name="is_filterable" id="is_filterable" label="Filterbar"></f-switch>
        </div>
        <div class="flex space-x-3 items-center mt-6 mb-2">
            <checkboxes-label>Untertätigkeiten</checkboxes-label>
            <icon-button icon="plus" v-if="mode === 'edit'" @click.prevent="addingSubactivity = true">Neu</icon-button>
        </div>
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
    </form>
</template>

<script>
export default {
    data: function () {
        return {
            addingSubactivity: false,
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
        'subactivity-form': () => import('./SubactivityForm.vue'),
        'popup': () => import('../../components/Popup.vue'),
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
                    _self.addingSubactivity = false;
                },
            });
        },
    },
};
</script>
