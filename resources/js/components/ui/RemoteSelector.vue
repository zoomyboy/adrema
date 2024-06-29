<template>
    <div>
        <f-select id="connection" v-model="innerConnection" label="Verbindung" name="connection" class="mt-2" :options="data"></f-select>

        <div v-if="innerConnection" class="mt-4">
            <div class="flex space-x-3 items-center bg-zinc-700 rounded-lg mt-3 py-1 px-2">
                <ui-sprite class="w-4 h-4 text-primary-700" src="open-folder"></ui-sprite>
                <div class="text-sm grow" v-text="structure.parent"></div>
                <ui-icon-button icon="undo" @click="emit('input', null)">löschen</ui-icon-button>
                <ui-icon-button icon="undo" @click="updateFiles(getParentDir(structure.parent))">Zurück</ui-icon-button>
            </div>
            <a
                v-for="(file, index) in structure.files"
                :key="index"
                href="#"
                class="flex space-x-3 items-center mt-1 transition duration-200 hover:bg-zinc-600 py-1 px-2 rounded"
                @click.prevent="updateFiles(file.path)"
            >
                <ui-sprite class="w-8 h-8 text-primary-700" src="open-folder"></ui-sprite>
                <span class="grow" :value="file.name">
                    {{ file.name }}
                </span>
                <button class="btn btn-primary btn-sm" @click.self.prevent.stop="select(file)">Auswählen</button>
                <ui-sprite class="w-3 h-3 -rotate-90 text-primary-400" src="chevron"></ui-sprite>
            </a>
        </div>
    </div>
</template>

<script setup>
import {ref, watch} from 'vue';
import {useApiIndex} from '../../composables/useApiIndex';

const {reload, data, axios} = useApiIndex('/api/fileshare');

const emit = defineEmits(['input']);

const props = defineProps({
    value: {
        validator: (v) => typeof v === 'object' || v === null,
        required: true,
    },
});

const innerConnection = ref(props.value === null ? null : props.value.connection_id);

const structure = ref({
    parent: props.value === null ? '/' : getParentDir(props.value.resource),
    files: [],
});

function select(file) {
    emit('input', {
        connection_id: innerConnection.value,
        resource: file.path,
    });
}

function getParentDir(dir) {
    if (!dir) {
        return '/';
    }
    return '/' + dir.split('/').slice(1, -1).join('/');
}

watch(innerConnection, () => updateFiles('/'));

async function updateFiles(parentDir) {
    console.log(innerConnection);
    if (innerConnection.value === null) {
        structure.value = {
            parent: '/',
            files: [],
        };
        return;
    }

    const response = await axios.post(`/api/fileshare/${innerConnection.value}/files`, {
        parent: parentDir,
    });

    structure.value = {
        parent: parentDir,
        files: response.data.data,
    };
}

await reload();
updateFiles(structure.value.parent);
</script>
