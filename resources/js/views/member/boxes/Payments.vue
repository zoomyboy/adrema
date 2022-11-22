<template>
    <table cellspacing="0" cellpadding="0" border="0" class="custom-table custom-table-sm text-sm">
        <thead>
            <th>Tätigkeit</th>
            <th>Untertätigkeit</th>
            <th>Datum</th>
        </thead>
        <tr v-for="(membership, index) in inner.memberships" :key="index">
            <td v-text="membership.activity_name"></td>
            <td v-text="membership.subactivity_name"></td>
            <td v-text="membership.human_date"></td>
        </tr>
    </table>
</template>

<script>
export default {
    props: {
        inner: {},
    },
};
</script>
