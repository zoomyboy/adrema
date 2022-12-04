<template>
    <div class="flex space-x-1">
        <i-link :href="member.links.show" class="inline-flex btn btn-primary btn-sm" v-tooltip="`Details`"
            ><svg-sprite src="eye"></svg-sprite
        ></i-link>
        <i-link :href="`/member/${member.id}/edit`" class="inline-flex btn btn-warning btn-sm" v-tooltip="`bearbeiten`"
            ><svg-sprite src="pencil"></svg-sprite
        ></i-link>
        <a
            href="#"
            v-tooltip="`Zahlungen`"
            v-show="hasModule('bill')"
            @click.prevent="$emit('sidebar', 'payment.index')"
            class="inline-flex btn btn-info btn-sm"
            ><svg-sprite src="money"></svg-sprite
        ></a>
        <a
            href="#"
            v-tooltip="`Ausbildungen`"
            v-show="hasModule('courses')"
            @click.prevent="$emit('sidebar', 'courses.index')"
            class="inline-flex btn btn-info btn-sm"
            ><svg-sprite src="course"></svg-sprite
        ></a>
        <a
            href="#"
            v-tooltip="`Mitgliedschaften`"
            @click.prevent="$emit('sidebar', 'courses.index')"
            class="inline-flex btn btn-info btn-sm"
            ><svg-sprite src="user"></svg-sprite
        ></a>
        <a
            :href="member.efz_link"
            v-show="member.efz_link"
            class="inline-flex btn btn-info btn-sm"
            v-tooltip="`EFZ Formular`"
            ><svg-sprite src="report"></svg-sprite
        ></a>
        <i-link
            href="#"
            @click.prevent="$emit('remove')"
            class="inline-flex btn btn-danger btn-sm"
            v-tooltip="`Entfernen`"
            ><svg-sprite src="trash"></svg-sprite
        ></i-link>
    </div>
</template>

<script>
export default {
    props: {
        member: {},
    },
};
</script>
