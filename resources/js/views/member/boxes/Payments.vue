<template>
    <div>
        <table cellspacing="0" cellpadding="0" border="0" class="hidden md:table custom-table custom-table-sm text-sm">
            <thead>
                <th>Beschreibung</th>
                <th>Beitrag</th>
                <th>Status</th>
            </thead>
            <tr v-for="(position, index) in inner" :key="index">
                <td v-text="position.description"></td>
                <td v-text="position.price_human"></td>
                <td v-text="position.invoice.status"></td>
            </tr>
        </table>

        <div class="md:hidden grid gap-3">
            <ui-box v-for="(position, index) in inner" :key="index" class="relative" :heading="position.description" second>
                <div class="text-xs text-gray-200" v-text="position.price_human"></div>
                <div class="text-xs text-gray-200" v-text="position.invoice.status"></div>
            </ui-box>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        value: {},
    },
    data: function () {
        return {
            inner: [],
        };
    },

    created() {
        this.inner = this.value;
    },
};
</script>
