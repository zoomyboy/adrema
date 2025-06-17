<template>
    <page-layout>
        <template #toolbar>
            <page-toolbar-button :href="meta.links.index" color="primary" icon="undo">zurück</page-toolbar-button>
        </template>
        <template #right>
            <f-save-button form="actionform" />
        </template>
        <form id="actionform" class="grow p-3" @submit.prevent="submit">
            <ui-popup v-if="mode === 'edit' && currentSubactivity !== null" heading="Neue Untertätigkeit" @close="currentSubactivity = null">
                <subactivity-form v-if="currentSubactivity" class="mt-4" :value="currentSubactivity" @stored="reloadSubactivities" @updated="mergeSubactivity" />
            </ui-popup>
            <div class="flex space-x-3">
                <f-text id="name" v-model="inner.name" label="Name" required />
                <f-switch id="is_filterable" v-model="inner.is_filterable" name="is_filterable" label="Filterbar" />
            </div>
            <div class="flex space-x-3 items-center mt-6 mb-2">
                <f-checkboxes-label>Untertätigkeiten</f-checkboxes-label>
                <ui-icon-button v-if="mode === 'edit'" icon="plus" @click.prevent="currentSubactivity = inner.subactivity_model">Neu</ui-icon-button>
            </div>
            <div class="grid gap-2 sm:grid-cols-2 md:grid-cols-4">
                <div v-for="option in subactivities" class="flex items-center space-x-2">
                    <a v-if="mode === 'edit'"
                       href="#"
                       class="transition hover:bg-yellow-600 group w-5 h-5 rounded-full flex items-center justify-center flex-none"
                       @click.prevent="currentSubactivity = option"
                    >
                        <ui-sprite src="pencil" class="text-yellow-800 w-3 h-3 group-hover:text-yellow-200 transition" />
                    </a>
                    <f-switch :id="`subactivities-${option.id}`"
                              :key="option.id"
                              v-model="inner.subactivities"
                              inline
                              size="sm"
                              name="subactivities[]"
                              :value="option.id"
                              :label="option.name"
                    />
                </div>
            </div>
        </form>
    </page-layout>
</template>

<script>
import {defineAsyncComponent} from 'vue';
import {useToast} from 'vue-toastification';

export default {

    props: {
        data: {},
        meta: {},
    },
    setup() {
        const toast = useToast();

        return {toast};
    },
    data: function () {
        return {
            currentSubactivity: null,
            subactivities: [...this.meta.subactivities],
            inner: {...this.data},
            mode: this.data.name === '' ? 'create' : 'edit',
        };
    },

    components: {
        'subactivity-form': defineAsyncComponent(() => import('./SubactivityForm.vue')),
    },

    methods: {
        submit() {
            this.mode === 'create' ? this.$inertia.post('/activity', this.inner) : this.$inertia.patch(`/activity/${this.inner.id}`, this.inner);
        },

        reloadSubactivities(model) {
            this.$inertia.reload({
                onSuccess: (page) => {
                    this.subactivities = page.props.meta.subactivities;
                    this.inner.subactivities.push(model.id);
                    this.toast.success('Untertätigkeit gespeichert.');
                    this.currentSubactivity = null;
                },
            });
        },

        mergeSubactivity(model) {
            this.$inertia.reload({
                onSuccess: (page) => {
                    this.subactivities = page.props.meta.subactivities;
                    this.inner.subactivities = this.inner.subactivities.map((s) => (s.id === model.id ? model : s));
                    this.toast.success('Untertätigkeit aktualisiert.');
                    this.currentSubactivity = null;
                },
            });
        },
    },
};
</script>
