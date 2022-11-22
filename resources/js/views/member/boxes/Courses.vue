<template>
    <div>
        <table
            cellspacing="0"
            cellpadding="0"
            border="0"
            class="custom-table overflow-auto custom-table-sm text-sm"
            v-if="inner.courses.length"
        >
            <thead>
                <th>Datum</th>
                <th>Baustein</th>
                <th>Veranstaltung</th>
                <th>Organisator</th>
            </thead>
            <tr v-for="(course, index) in inner.courses" :key="index">
                <td v-text="course.completed_at_human"></td>
                <td v-text="course.course.short_name"></td>
                <td v-text="course.event_name"></td>
                <td v-text="course.organizer"></td>
            </tr>
        </table>
        <div class="py-3 text-gray-400 text-center" v-else>Keine Ausbildungen vorhanden</div>
    </div>
</template>

<script>
export default {
    props: {
        inner: {},
    },
};
</script>
