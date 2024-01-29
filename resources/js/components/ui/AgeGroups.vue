<template>
    <div class="flex gap-1 justify-center items-center">
        <ui-sprite v-if="member.is_leader" class="flex-none" :class="[ageColors.leiter, iconClass]" src="lilie"></ui-sprite>
        <ui-sprite v-if="member.age_group_icon" class="flex-none" :class="[ageColors[member.age_group_icon], iconClass]" src="lilie"></ui-sprite>
    </div>
</template>

<script setup>
import useAgeColors from '../../composables/useAgeColors.js';

const {ageColors} = useAgeColors();

defineProps({
    iconClass: {
        default: 'w-3 h-3',
    },
    member: {
        required: true,
    },
});
</script>
