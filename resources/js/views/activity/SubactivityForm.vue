<template>
    <div>
        <div class="flex space-x-3">
            <f-text size="sm" id="name" v-model="model.name" label="Name" required></f-text>
            <f-switch size="sm" v-model="model.is_filterable" name="subactivity_is_filterable" id="subactivity_is_filterable" label="Filterbar"></f-switch>
        </div>
        <icon-button class="mt-3" icon="save" @click.prevent="store">Speichern</icon-button>
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
            try {
                var response = await this.axios.post('/subactivity', this.model);
                this.$emit('stored', response.data);
            } catch (e) {
                this.errorsFromException(e);
            }
        },
    },
};
</script>
