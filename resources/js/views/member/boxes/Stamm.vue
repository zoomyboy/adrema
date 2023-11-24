<template>
    <div class="grid grid-cols-2 gap-3">
        <ui-text-display class="col-span-2" label="Name" :value="inner.fullname"></ui-text-display>
        <ui-text-display class="col-span-2" label="Adresse" :value="inner.full_address"></ui-text-display>
        <ui-text-display label="Geburtsdatum" :value="inner.birthday_human"></ui-text-display>
        <ui-text-display label="Alter" :value="inner.age"></ui-text-display>
        <ui-text-display label="Bundesland" :value="inner.region.name"></ui-text-display>
        <ui-text-display label="Nationalität" :value="inner.nationality?.name"></ui-text-display>
        <ui-text-display v-show="inner.other_country" label="Andere Staatsangehörigkeit" :value="inner.other_country"></ui-text-display>
        <ui-text-display class="col-span-2" label="Stammgruppierung" :value="inner.group_name"></ui-text-display>
    </div>
</template>

<script>
export default {
    props: {
        inner: {},
    },
};
</script>
