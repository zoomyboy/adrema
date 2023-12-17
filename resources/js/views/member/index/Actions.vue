<template>
    <div class="flex space-x-1">
        <i-link v-tooltip="`Details`" :href="member.links.show" class="inline-flex btn btn-primary btn-sm"><ui-sprite
                src="eye"></ui-sprite></i-link>
        <i-link v-tooltip="`Bearbeiten`" :href="member.links.edit" class="inline-flex btn btn-warning btn-sm"><ui-sprite
                src="pencil"></ui-sprite></i-link>
        <a v-show="hasModule('bill')" v-tooltip="`Zahlungen`" href="#" class="inline-flex btn btn-info btn-sm"
            @click.prevent="$emit('sidebar', 'invoicePosition')"><ui-sprite src="money"></ui-sprite></a>
        <a v-show="hasModule('courses')" v-tooltip="`Ausbildungen`" href="#" class="inline-flex btn btn-info btn-sm"
            @click.prevent="$emit('sidebar', 'courses')"><ui-sprite src="course"></ui-sprite></a>
        <a v-tooltip="`Mitgliedschaften`" href="#" class="inline-flex btn btn-info btn-sm"
            @click.prevent="$emit('sidebar', 'membership')"><ui-sprite src="user"></ui-sprite></a>
        <a v-show="member.efz_link" v-tooltip="`EFZ Formular`" :href="member.efz_link"
            class="inline-flex btn btn-info btn-sm"><ui-sprite src="report"></ui-sprite></a>
        <a v-tooltip="`Entfernen`" href="#" class="inline-flex btn btn-danger btn-sm"
            @click.prevent="$emit('remove')"><ui-sprite src="trash"></ui-sprite></a>
    </div>
</template>

<script setup>
defineProps({
    member: {
        type: Object,
        required: true,
    },
});
</script>
