<template>
    <div class="flex space-x-1">
        <i-link :href="member.links.show" class="inline-flex btn btn-primary btn-sm" v-tooltip="`Details`"><ui-sprite src="eye"></ui-sprite></i-link>
        <i-link :href="`/member/${member.id}/edit`" class="inline-flex btn btn-warning btn-sm" v-tooltip="`Bearbeiten`"><ui-sprite src="pencil"></ui-sprite></i-link>
        <a href="#" v-tooltip="`Zahlungen`" v-show="hasModule('bill')" @click.prevent="$emit('sidebar', 'payment.index')" class="inline-flex btn btn-info btn-sm"
            ><ui-sprite src="money"></ui-sprite
        ></a>
        <a href="#" v-tooltip="`Ausbildungen`" v-show="hasModule('courses')" @click.prevent="$emit('sidebar', 'courses.index')" class="inline-flex btn btn-info btn-sm"
            ><ui-sprite src="course"></ui-sprite
        ></a>
        <a href="#" v-tooltip="`Mitgliedschaften`" @click.prevent="$emit('sidebar', 'membership.index')" class="inline-flex btn btn-info btn-sm"><ui-sprite src="user"></ui-sprite></a>
        <a :href="member.efz_link" v-show="member.efz_link" class="inline-flex btn btn-info btn-sm" v-tooltip="`EFZ Formular`"><ui-sprite src="report"></ui-sprite></a>
        <a href="#" @click.prevent="$emit('remove')" class="inline-flex btn btn-danger btn-sm" v-tooltip="`Entfernen`"><ui-sprite src="trash"></ui-sprite></a>
    </div>
</template>

<script>
export default {
    props: {
        member: {},
    },
};
</script>
