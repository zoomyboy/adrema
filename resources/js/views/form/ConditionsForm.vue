<template>
    <div>
        <conditions :id="id" v-model="inner" :single="single"></conditions>
        <ui-icon-button class="mt-4 mb-2" icon="save" @click="save">Speichern</ui-icon-button>
    </div>
</template>

<script lang="js" setup>
import { ref, inject, computed } from 'vue';
const emit = defineEmits(['save']);
import Conditions from './Conditions.vue';

const props = defineProps({
    value: {
        required: true,
    },
    single: {
        required: true,
    },
    id: {
        required: true,
        type: String,
    }
});

const inner = ref(JSON.parse(JSON.stringify(props.value)));

async function save() {
    emit('save', inner.value);
}
</script>
