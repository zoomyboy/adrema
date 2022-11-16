<template>
    <div>
        <table cellspacing="0" cellpadding="0" border="0" class="custom-table custom-table-sm">
            <thead>
                <th>Name</th>
                <th>Interner Beitrag</th>
                <th>Nami-Beitrag</th>
                <th></th>
            </thead>

            <tr v-for="(subscription, index) in data" :key="index">
                <td>
                    <div v-text="subscription.name"></div>
                </td>
                <td>
                    <div v-text="subscription.amount_human"></div>
                </td>
                <td>
                    <div v-text="subscription.fee_name"></div>
                </td>
                <td>
                    <i-link :href="`/subscription/${subscription.id}/edit`" class="inline-flex btn btn-warning btn-sm"
                        ><svg-sprite src="pencil"></svg-sprite
                    ></i-link>
                </td>
            </tr>
        </table>
    </div>
</template>

<script>
export default {
    props: {
        data: {},
    },
};
</script>
