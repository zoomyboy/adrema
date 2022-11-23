<template>
    <div class="grid grid-cols-2 gap-3">
        <key-value class="col-span-2" label="Name" :value="inner.fullname"></key-value>
        <key-value class="col-span-2" label="Adresse" :value="inner.full_address"></key-value>
        <key-value label="Geburtsdatum" :value="inner.birthday_human"></key-value>
        <key-value label="Alter" :value="inner.age"></key-value>
        <key-value label="Bundesland" :value="inner.region.name"></key-value>
        <key-value label="Nationalität" :value="inner.nationality.name"></key-value>
        <key-value
            v-show="inner.other_country"
            label="Andere Staatsangehörigkeit"
            :value="inner.other_country"
        ></key-value>
    </div>
</template>

<script>
export default {
    props: {
        inner: {},
    },
    components: {
        'key-value': () => import(/* webpackChunkName: "member" */ '../KeyValue'),
    },
};
</script>
