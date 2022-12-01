<template>
    <div>
        <table
            cellspacing="0"
            cellpadding="0"
            border="0"
            class="hidden md:table custom-table overflow-auto custom-table-sm text-sm"
        >
            <thead>
                <th>Tätigkeit</th>
                <th>Untertätigkeit</th>
                <th>Datum</th>
            </thead>
            <tr v-for="(membership, index) in inner" :key="index">
                <td v-text="membership.activity_name"></td>
                <td v-text="membership.subactivity_name"></td>
                <td v-text="membership.human_date"></td>
            </tr>
        </table>

        <div class="md:hidden grid gap-3">
            <box
                class="relative"
                :heading="membership.activity_name"
                v-for="(membership, index) in inner"
                :key="index"
                second
            >
                <div class="text-xs text-gray-200" v-text="membership.subactivity_name"></div>
                <div class="text-xs text-gray-200" v-text="membership.human_date"></div>
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
