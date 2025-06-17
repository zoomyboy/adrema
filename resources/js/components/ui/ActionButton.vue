<template>
    <a v-tooltip="tooltip" :href="href" :target="blank ? '_BLANK' : '_SELF'" class="inline-flex btn btn-sm">
        <ui-sprite :src="icon" />
    </a>
</template>

<script lang="ts" setup>
withDefaults(defineProps<{
    tooltip: string;
    href?: string;
    blank?: boolean,
    icon: string,
}>(), {href: '#', blank: false});
</script>
