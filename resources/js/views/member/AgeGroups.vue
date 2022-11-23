<template>
    <div class="flex gap-1 justify-center items-center">
        <svg-sprite
            class="flex-none"
            v-if="member.is_leader"
            :class="[ageColors.leiter, iconClass]"
            src="lilie"
        ></svg-sprite>
        <svg-sprite
            class="flex-none"
            v-if="member.age_group_icon"
            :class="[ageColors[member.age_group_icon], iconClass]"
            src="lilie"
        ></svg-sprite>
    </div>
</template>

<script>
export default {
    data: function () {
        return {
            ageColors: {
                biber: 'text-biber',
                woelfling: 'text-woelfling',
                jungpfadfinder: 'text-jungpfadfinder',
                pfadfinder: 'text-pfadfinder',
                rover: 'text-rover',
                leiter: 'text-leiter',
            },
        };
    },
    props: {
        iconClass: {
            default: 'w-3 h-3',
        },
        member: {
            required: true,
        },
    },
};
</script>
