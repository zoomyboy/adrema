<template>
    <div class="grid gap-3">
        <key-value v-show="inner.main_phone" label="Telefon Eltern" :value="inner.main_phone" type="tel"></key-value>
        <key-value v-show="inner.mobile_phone" label="Handy Eltern" :value="inner.mobile_phone" type="tel"></key-value>
        <key-value
            v-show="inner.work_phone"
            label="Telefon Eltern geschäftlich"
            :value="inner.work_phone"
            type="tel"
        ></key-value>
        <key-value
            v-show="inner.children_phone"
            label="Telefon Kind"
            :value="inner.children_phone"
            type="tel"
        ></key-value>
        <key-value v-show="inner.email" label="E-Mail-Adresse Kind" :value="inner.email" type="email"></key-value>
        <key-value
            v-show="inner.email_parents"
            label="E-Mail-Adresse Eltern"
            :value="inner.email_parents"
            type="email"
        ></key-value>
        <key-value v-show="inner.fax" label="Fax" :value="inner.fax" type="tel"></key-value>
    </div>
</template>

<script>
export default {
    props: {
        inner: {},
    },
    components: {
        'key-value': () => import('../KeyValue'),
    },
};
</script>
