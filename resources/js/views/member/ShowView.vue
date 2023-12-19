<template>
    <page-layout>
        <template #toolbar>
            <page-toolbar-button :href="meta.links.index" color="primary" icon="undo">zurück</page-toolbar-button>
            <page-toolbar-button :href="data.links.edit" color="warning" icon="pencil">bearbeiten</page-toolbar-button>
        </template>
        <div class="p-3 grid gap-3 this-grid grow">
            <ui-box heading="Stammdaten" class="area-stamm hidden xl:block">
                <stamm :inner="inner"></stamm>
            </ui-box>
            <ui-box heading="Kontakt" class="area-kontakt hidden xl:block">
                <kontakt :inner="inner"></kontakt>
            </ui-box>
            <ui-box class="area-stammkontakt block xl:hidden">
                <tabs v-model="tabs.stammkontakt">
                    <stamm v-show="tabs.stammkontakt.active === 'stamm'" :inner="inner"></stamm>
                    <kontakt v-show="tabs.stammkontakt.active === 'kontakt'" :inner="inner"></kontakt>
                </tabs>
            </ui-box>

            <ui-box container-class="" heading="Prävention" class="area-praev hidden xl:block">
                <prae :inner="inner"></prae>
            </ui-box>
            <ui-box heading="System" class="area-system hidden xl:block">
                <system :inner="inner"></system>
            </ui-box>
            <ui-box class="area-praesystem block xl:hidden">
                <tabs v-model="tabs.praesystem">
                    <prae v-show="tabs.praesystem.active === 'prae'" :inner="inner"></prae>
                    <system v-show="tabs.praesystem.active === 'system'" :inner="inner"></system>
                </tabs>
            </ui-box>

            <ui-box class="area-membershipcourse hidden xl:block">
                <tabs v-model="tabs.membershipcourse">
                    <courses v-show="tabs.membershipcourse.active === 'course'" :value="inner.courses"></courses>
                    <memberships v-show="tabs.membershipcourse.active === 'membership'" :value="inner.memberships"> </memberships>
                </tabs>
            </ui-box>
            <ui-box heading="Ausbildungen" class="area-courses xl:hidden">
                <courses :value="inner.courses"></courses>
            </ui-box>
            <ui-box heading="Mitgliedschaften" class="area-memberships xl:hidden">
                <memberships :value="inner.memberships"></memberships>
            </ui-box>

            <ui-box heading="Zahlungen" class="area-payments">
                <payments :value="inner.invoicePositions"></payments>
            </ui-box>

            <ui-box heading="Karte" container-class="grow" class="area-map hidden xl:flex">
                <div class="h-full flex items-center justify-center text-gray-400 text-center">Keine Karte vorhanden</div>
            </ui-box>
        </div>
    </page-layout>
</template>

<script>
import {defineAsyncComponent} from 'vue';

export default {
    props: {
        data: {},
        meta: {},
    },
    data: function () {
        return {
            inner: {},
            tabs: {
                stammkontakt: {
                    children: {
                        stamm: 'Stammdaten',
                        kontakt: 'Kontakt',
                    },
                    active: 'stamm',
                },
                praesystem: {
                    children: {
                        system: 'System',
                        prae: 'Prävention',
                    },
                    active: 'system',
                },
                membershipcourse: {
                    children: {
                        membership: 'Mitgliedschaften',
                        course: 'Ausbildungen',
                    },
                    active: 'membership',
                },
            },
        };
    },

    methods: {},

    components: {
        stamm: defineAsyncComponent(() => import('./boxes/Stamm.vue')),
        kontakt: defineAsyncComponent(() => import('./boxes/Kontakt.vue')),
        prae: defineAsyncComponent(() => import('./boxes/Prae.vue')),
        courses: defineAsyncComponent(() => import('./boxes/Courses.vue')),
        system: defineAsyncComponent(() => import('./boxes/System.vue')),
        payments: defineAsyncComponent(() => import('./boxes/Payments.vue')),
        memberships: defineAsyncComponent(() => import('./boxes/Memberships.vue')),
        tabs: defineAsyncComponent(() => import('./Tabs.vue')),
    },

    created() {
        this.inner = this.data;
    },
};
</script>

<style scoped>
.this-grid {
    grid-template-areas:
        'stammkontakt'
        'praesystem'
        'courses'
        'memberships'
        'payments';
    grid-template-columns: 1fr;
}

@media screen and (min-width: 1280px) {
    .this-grid {
        grid-template-areas:
            'stamm kontakt praev system'
            'membershipcourse membershipcourse membershipcourse membershipcourse'
            'payments payments map map';
        grid-template-columns: max-content max-content max-content 1fr;
    }
}

.area-stamm {
    grid-area: stamm;
}

.area-kontakt {
    grid-area: kontakt;
}

.area-praev {
    grid-area: praev;
}

.area-courses {
    grid-area: courses;
}

.area-system {
    grid-area: system;
}

.area-memberships {
    grid-area: memberships;
}

.area-payments {
    grid-area: payments;
}

.area-map {
    grid-area: map;
}

.area-stammkontakt {
    grid-area: stammkontakt;
}

.area-membershipcourse {
    grid-area: membershipcourse;
}

.area-praesystem {
    grid-area: praesystem;
}
</style>
