<template>
    <div>
        <table
            cellspacing="0"
            cellpadding="0"
            border="0"
            class="hidden md:table custom-table overflow-auto custom-table-sm text-sm"
            v-if="inner.length"
        >
            <thead>
                <th>Datum</th>
                <th>Baustein</th>
                <th>Veranstaltung</th>
                <th>Organisator</th>
            </thead>
            <tr v-for="(course, index) in inner" :key="index">
                <td v-text="course.completed_at_human"></td>
                <td v-text="course.course.short_name"></td>
                <td v-text="course.event_name"></td>
                <td v-text="course.organizer"></td>
            </tr>
        </table>
        <div class="py-3 text-gray-400 text-center" v-else>Keine Ausbildungen vorhanden</div>
        <div class="md:hidden grid gap-3">
            <box
                class="relative"
                :heading="course.course.short_name"
                v-for="(course, index) in inner"
                :key="index"
                second
            >
                <div class="text-xs text-gray-200" v-text="course.event_name"></div>
                <div class="text-xs text-gray-200" v-text="course.completed_at_human"></div>
                <div class="text-xs text-gray-200" v-text="course.organizer"></div>
            </box>
        </div>
    </div>
</template>

<script>
export default {
    data: function () {
        return {
            inner: [],
        };
    },
    props: {
        value: {},
    },
    created() {
        this.inner = this.value;
    },
};
</script>
