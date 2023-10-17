<template>
    <div class="sidebar flex flex-col group is-bright">
        <page-header title="Ausbildungen" @close="$emit('close')">
            <template #toolbar>
                <page-toolbar-button v-if="single === null" color="primary" icon="plus" @click.prevent="create">Neue
                    Ausbildung</page-toolbar-button>
                <page-toolbar-button v-if="single !== null" color="primary" icon="undo"
                    @click.prevent="cancel">Zurück</page-toolbar-button>
            </template>
        </page-header>

        <form v-if="single" class="p-6 grid gap-4 justify-start" @submit.prevent="submit">
            <f-text id="completed_at" v-model="single.completed_at" type="date" label="Datum" required></f-text>
            <f-select id="course_id" v-model="single.course_id" name="course_id" :options="meta.courses" label="Baustein"
                required></f-select>
            <f-text id="event_name" v-model="single.event_name" label="Veranstaltung" required></f-text>
            <f-text id="organizer" v-model="single.organizer" label="Veranstalter" required></f-text>
            <button type="submit" class="btn btn-primary">Absenden</button>
        </form>

        <div v-else class="grow">
            <table class="custom-table custom-table-light custom-table-sm text-sm grow">
                <thead>
                    <th>Baustein</th>
                    <th>Veranstaltung</th>
                    <th>Veranstalter</th>
                    <th>Datum</th>
                    <th></th>
                </thead>

                <tr v-for="(course, index) in data" :key="index">
                    <td v-text="course.course_name"></td>
                    <td v-text="course.event_name"></td>
                    <td v-text="course.organizer"></td>
                    <td v-text="course.completed_at_human"></td>
                    <td class="flex">
                        <a href="#" class="inline-flex btn btn-warning btn-sm" @click.prevent="edit(course)"><ui-sprite
                                src="pencil"></ui-sprite></a>
                        <a href="#" class="inline-flex btn btn-danger btn-sm" @click.prevent="remove(course)"><ui-sprite
                                src="trash"></ui-sprite></a>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</template>

<script setup>
defineEmits(['close']);
import { useApiIndex } from '../../composables/useApiIndex.js';

const props = defineProps({
    url: {
        type: String,
        required: true,
    },
});

const { data, meta, reload, cancel, single, create, edit, submit, remove } = useApiIndex(props.url, 'course');

await reload();
</script>
