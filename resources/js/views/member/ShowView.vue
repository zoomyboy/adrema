<template>
    <page-layout>
        <template #toolbar>
            <page-toolbar-button :href="meta.links.index" color="primary" icon="undo">zurück</page-toolbar-button>
            <page-toolbar-button :href="data.links.edit" color="warning" icon="pencil">bearbeiten</page-toolbar-button>
        </template>
        <div class="p-3 grid gap-3 xl:grid-rows-[max-content_1fr] xl:grid-cols-[max-content_max-content_max-content_1fr] grow">
            <section class="mobile hidden xl:contents">
                <ui-box heading="Stammdaten">
                    <stamm :inner="props.data" />
                </ui-box>
                <ui-box heading="Kontakt">
                    <kontakt :inner="props.data" />
                </ui-box>
                <ui-box heading="Prävention">
                    <prae :inner="props.data" />
                </ui-box>
                <ui-box heading="System">
                    <system :inner="props.data" />
                </ui-box>
                <ui-box class="col-span-full">
                    <ui-tabs v-model="tabs.membershipcourse.active" :entries="tabs.membershipcourse.entries" />
                    <memberships v-show="tabs.membershipcourse.active === 0" :value="props.data.memberships" />
                    <courses v-show="tabs.membershipcourse.active === 1" :value="props.data.courses" />
                    <payments v-show="tabs.membershipcourse.active === 2" :value="props.data.invoicePositions" />
                    <div v-show="tabs.membershipcourse.active === 3" class="h-full flex items-center justify-center text-gray-400 text-center">Keine Karte vorhanden</div>
                </ui-box>
            </section>

            <section class="mobile contents xl:hidden">
                <ui-box heading="Stammdaten"> <stamm :inner="props.data" /> </ui-box>
                <ui-box heading="Kontakt"> <kontakt :inner="props.data" /> </ui-box>
                <ui-box heading="Prävention"> <prae :inner="props.data" /> </ui-box>
                <ui-box heading="System"> <system :inner="props.data" /> </ui-box>
                <ui-box heading="Mitgliedschaften"> <memberships :value="props.data.memberships" /> </ui-box>
                <ui-box heading="Ausbildungen"> <courses :value="props.data.courses" /> </ui-box>
                <ui-box heading="Zahlungen"> <payments :value="props.data.invoicePositions" /> </ui-box>
                <ui-box heading="Karte"> <div class="h-full flex items-center justify-center text-gray-400 text-center">Keine Karte vorhanden</div> </ui-box>
            </section>
        </div>
    </page-layout>
</template>

<script lang="ts" setup>
import {defineAsyncComponent, ref} from 'vue';

const stamm = defineAsyncComponent(() => import('./boxes/Stamm.vue'));
const kontakt = defineAsyncComponent(() => import('./boxes/Kontakt.vue'));
const prae = defineAsyncComponent(() => import('./boxes/Prae.vue'));
const courses = defineAsyncComponent(() => import('./boxes/Courses.vue'));
const system = defineAsyncComponent(() => import('./boxes/System.vue'));
const payments = defineAsyncComponent(() => import('./boxes/Payments.vue'));
const memberships = defineAsyncComponent(() => import('./boxes/Memberships.vue'));

const tabs = ref({
    stammkontakt: {
        active: 0,
        entries: [{title: 'Stammdaten'}, {title: 'Kontakt'}],
    },
    praesystem: {
        active: 0,
        entries: [{title: 'System'}, {title: 'Prävention'}],
    },
    membershipcourse: {
        active: 0,
        entries: [{title: 'Mitgliedschaften'}, {title: 'Ausbildungen'}, {title: 'Zahlungen'}, {title: 'Karte'}],
    },
});

const props = defineProps<{
    data: object,
    meta: object,
}>();
</script>
