<template>
    <form id="actionform" class="grow  p-3" @submit.prevent="submit">
        <f-text id="name" v-model="inner.name" label="Name" required></f-text>
        <checkboxes-label class="mt-4">Untertätigkeiten</checkboxes-label>
        <div class="grid gap-2 sm:grid-cols-2 md:grid-cols-4">
            <f-switch inline size="sm" :key="option.id" v-model="inner.subactivities" name="subactivities[]" :id="`subactivities-${option.id}`" :value="option.id" :label="option.name" v-for="option in meta.subactivities"></f-switch>
        </div>
        <save-button form="actionform"></save-button>
    </form>
</template>

<script>
export default {
    data: function () {
        return {
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
    },

    methods: {
        submit() {
            this.mode === 'create'
            ? this.$inertia.post('/activity', this.inner)
            : this.$inertia.patch(`/activity/${this.inner.id}`, this.inner);
        },
    }

};
</script>
