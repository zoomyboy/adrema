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
            <f-select id="course_id" v-model="single.course_id" name="course_id" :options="courses" label="Baustein"
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

                <tr v-for="(course, index) in value.courses" :key="index">
                    <td v-text="course.course_name"></td>
                    <td v-text="course.event_name"></td>
                    <td v-text="course.organizer"></td>
                    <td v-text="course.completed_at_human"></td>
                    <td class="flex">
                        <a href="#" class="inline-flex btn btn-warning btn-sm" @click.prevent="
                            single = course;
                        mode = 'edit';
                                                        "><ui-sprite src="pencil"></ui-sprite></a>
                        <i-link href="#" class="inline-flex btn btn-danger btn-sm"
                            @click.prevent="remove(course)"><ui-sprite src="trash"></ui-sprite></i-link>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</template>

<script>
export default {

    props: {
        courses: {},
        value: {},
    },
    data: function () {
        return {
            mode: null,
            single: null,
        };
    },

    methods: {
        create() {
            this.mode = 'create';
            this.single = {};
        },
        cancel() {
            this.mode = this.single = null;
        },
        remove(payment) {
            this.$inertia.delete(`/member/${this.value.id}/course/${payment.id}`);
        },

        openLink(link) {
            if (link.disabled) {
                return;
            }

            window.open(link.href);
        },

        submit() {
            var _self = this;

            this.mode === 'create'
                ? this.$inertia.post(`/member/${this.value.id}/course`, this.single, {
                    onFinish() {
                        _self.single = null;
                    },
                })
                : this.$inertia.patch(`/member/${this.value.id}/course/${this.single.id}`, this.single, {
                    onFinish() {
                        _self.single = null;
                    },
                });
        },
    },
};
</script>
