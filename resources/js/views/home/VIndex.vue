<template>
    <div class="gap-6 grid-cols-4 grid p-6">
        <v-block title="Ausstehende Mitgliedsbeiträge">
            <div class="text-gray-100">
                <span class="text-xl mr-1 font-semibold" v-text="data.payments.amount"></span>
                <span class="text-sm" v-text="`von ${data.payments.users} / ${data.payments.all_users} Mitgliedern`"></span>
            </div>
        </v-block>
        <v-block title="Gruppierungs-Verteilung">
            <div v-for="group, index in data.groups" :key="index" class="flex mt-2 items-center leading-none text-gray-100">
                <sprite class="w-4 h-4 mr-2" src="lilie" :class="`text-${group.slug}`"></sprite>
                <span v-text="group.name" class="grow"></span>
                <span v-text="group.count"></span>
            </div>
        </v-block>
        <v-block title="Endende Schhnupperzeiten">
            <div v-for="member, index in data.ending_tries" :key="index" class="flex mt-2 items-center leading-none text-gray-100">
                <span class="grow" v-text="`${member.firstname} ${member.lastname}`"></span>
                <span class="mr-2 text-sm tex-gray-600" v-text="`${member.try_ends_at}`"></span>
                <span class="text-xs tex-gray-600" v-text="`${member.try_ends_at_human}`"></span>
            </div>
        </v-block>
    </div>
</template>

<script>
export default {
    props: {
        data: {},
        blocks: {}
    },

    components: {
        'VBlock': () => import('./VBlock')
    }

};
</script>
