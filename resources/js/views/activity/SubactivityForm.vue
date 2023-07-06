<template>
    <div>
        <div class="flex space-x-3" v-if="model">
            <f-text size="sm" id="name" v-model="model.name" label="Name" required></f-text>
            <f-switch size="sm" v-model="model.is_filterable" name="subactivity_is_filterable" id="subactivity_is_filterable" label="Filterbar"></f-switch>
        </div>
        <ui-icon-button class="mt-3" icon="save" @click.prevent="store">Speichern</ui-icon-button>
    </div>
</template>

<script>
export default {
    data: function () {
        return {
            visible: false,
            model: {...this.value},
        };
    },
    props: {
        value: {
            required: true,
        },
    },

    methods: {
        async store() {
            if (this.model.id) {
                var response = await this.axios.patch(this.model.links.update, this.model);
                this.$emit('updated', response.data);
            } else {
                var response = await this.axios.post('/subactivity', this.model);
                this.$emit('stored', response.data);
            }
        },
    },

    async created() {
        if (this.value.id) {
            var payload = (await this.axios.get(this.value.links.show)).data;
            this.model = payload.data;
        } else {
            this.model = this.value;
        }
    },
};
</script>
