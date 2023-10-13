<template>
    <page-header title="Zahlungen" @close="$emit('close')">
        <template #toolbar>
            <page-toolbar-button v-if="single === null" color="primary" icon="plus" @click.prevent="create">Neue
                Zahlung</page-toolbar-button>
            <page-toolbar-button v-if="single !== null" color="primary" icon="undo"
                @click.prevent="cancel">Zurück</page-toolbar-button>
        </template>
    </page-header>

    <form v-if="single" class="p-6 grid gap-4 justify-start" @submit.prevent="submit">
        <f-text id="nr" v-model="single.nr" label="Jahr" required></f-text>
        <f-select id="subscription_id" v-model="single.subscription_id" name="subscription_id" :options="meta.subscriptions"
            label="Beitrag" required></f-select>
        <f-select id="status_id" v-model="single.status_id" name="status_id" :options="meta.statuses" label="Status"
            required></f-select>
        <button type="submit" class="btn btn-primary">Absenden</button>
    </form>

    <div v-else class="grow">
        <table class="custom-table custom-table-light custom-table-sm text-sm">
            <thead>
                <th>Nr</th>
                <th>Status</th>
                <th>Beitrag</th>
                <th></th>
            </thead>

            <tr v-for="(payment, index) in data" :key="index">
                <td v-text="payment.nr"></td>
                <td v-text="payment.status_name"></td>
                <td v-text="payment.subscription.name"></td>
                <td class="flex">
                    <a href="#" class="inline-flex btn btn-warning btn-sm" @click.prevent="edit(payment)"><ui-sprite
                            src="pencil"></ui-sprite></a>
                    <button v-show="!payment.is_accepted" href="#" class="inline-flex btn btn-success btn-sm"
                        @click.prevent="accept(payment)"><ui-sprite src="check"></ui-sprite></button>
                    <button class="inline-flex btn btn-danger btn-sm" @click.prevent="remove(payment)"><ui-sprite
                            src="trash"></ui-sprite></button>
                </td>
            </tr>
        </table>
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

const { axios, data, meta, reload, cancel, single, create, edit, submit, remove } = useApiIndex(props.url, 'payment');

async function accept(payment) {
    await axios.patch(payment.links.update, { ...payment, status_id: 3 });

    await reload();
}

await reload();
</script>
