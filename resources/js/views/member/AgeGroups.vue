<template>
    <div class="flex gap-1 justify-center items-center">
        <svg-sprite
            class="w-3 h-3 flex-none"
            v-if="member.is_leader"
            :class="ageColors.leiter"
            src="lilie"
        ></svg-sprite>
        <svg-sprite
            class="w-3 h-3 flex-none"
            v-if="member.age_group_icon"
            :class="ageColors[member.age_group_icon]"
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
        member: {
            required: true,
        },
    },
};
</script>
