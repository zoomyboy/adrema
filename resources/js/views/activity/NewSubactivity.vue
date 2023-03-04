<template>
    <div>
        <icon-button class="mt-4" icon="plus" v-show="model === null" @click.prevent="model = {name: '', is_filterable: false, activities: [activityId]}">Neue Untertätigkeit</icon-button>
        <icon-button class="mt-4" icon="close" v-show="model !== null" @click.prevent="model = null">Schließen</icon-button>
        <div class="mt-2 border border-primary-700 rounded-lg p-5" v-if="model !== null">
            <div class="flex space-x-3">
                <f-text size="sm" id="name" v-model="model.name" label="Name" required></f-text>
                <f-switch size="sm" v-model="model.is_filterable" name="subactivity_is_filterable" id="subactivity_is_filterable" label="Filterbar"></f-switch>
            </div>
            <icon-button class="mt-3" icon="save" @click.prevent="store">Speichern</icon-button>
        </div>
    </div>
</template>

<script>
export default {
    data: function () {
        return {
            visible: false,
            model: null,
        };
    },
    props: {
        activityId: {
            type: Number,
            required: true,
        },
    },

    methods: {
        async store() {
            var response = await this.axios.post('/subactivity', this.model);
            this.model = null;
            this.$emit('stored', response.data);
        },
    },
};
</script>
