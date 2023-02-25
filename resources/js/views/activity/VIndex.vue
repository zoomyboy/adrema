<template>
    <div class="pb-6">
        <table cellspacing="0" cellpadding="0" border="0" class="custom-table custom-table-sm table">
            <thead>
                <th>Name</th>
                <th></th>
            </thead>

            <tr v-for="(activity, index) in inner.data" :key="index">
                <td v-text="activity.name"></td>
                <td>
                    <div class="flex space-x-1">
                        <i-link :href="activity.links.edit" class="inline-flex btn btn-warning btn-sm" v-tooltip="`bearbeiten`"><svg-sprite src="pencil"></svg-sprite></i-link>
                        <i-link
                            href="#"
                            @click.prevent="$emit('remove')"
                            class="inline-flex btn btn-danger btn-sm"
                            v-tooltip="`Entfernen`"
                            ><svg-sprite src="trash"></svg-sprite
                        ></i-link>
                    </div>
                </td>
            </tr>
        </table>

        <div class="px-6">
            <v-pages class="mt-4" :value="data.meta" :only="['data']"></v-pages>
        </div>

    </div>
</template>

<script>
import indexHelpers from '../../mixins/indexHelpers';

export default {

    mixins: [indexHelpers],


};
</script>
