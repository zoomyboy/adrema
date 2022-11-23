<template>
    <div class="p-6 grid gap-6 this-grid grow">
        <box heading="Stammdaten" class="area-stamm hidden 2xl:block">
            <stamm :inner="inner"></stamm>
        </box>
        <box heading="Kontakt" class="area-kontakt hidden 2xl:block">
            <kontakt :inner="inner"></kontakt>
        </box>
        <box class="area-stammkontakt block 2xl:hidden">
            <tabs v-model="tabs.stammkontakt">
                <stamm v-show="tabs.stammkontakt.active === 'stamm'" :inner="inner"></stamm>
                <kontakt v-show="tabs.stammkontakt.active === 'kontakt'" :inner="inner"></kontakt>
            </tabs>
        </box>

        <box container-class="" heading="Prävention" class="area-praev hidden 2xl:block">
            <prae :inner="inner"></prae>
        </box>
        <box heading="System" class="area-system hidden 2xl:block">
            <system :inner="inner"></system>
        </box>
        <box class="area-praesystem block 2xl:hidden">
            <tabs v-model="tabs.praesystem">
                <prae v-show="tabs.praesystem.active === 'prae'" :inner="inner"></prae>
                <system v-show="tabs.praesystem.active === 'system'" :inner="inner"></system>
            </tabs>
        </box>

        <box heading="Ausbildungen" class="area-courses hidden 2xl:block">
            <courses :inner="inner"></courses>
        </box>

        <box heading="Mitgliedschaften" class="area-memberships hidden 2xl:block">
            <memberships :inner="inner"></memberships>
        </box>

        <box heading="Zahlungen" class="area-payments hidden 2xl:block">
            <payments :inner="inner"></payments>
        </box>

        <box heading="Karte" container-class="grow" class="area-map hidden 2xl:block">
            <vmap :inner="inner"></vmap>
        </box>
    </div>
</template>

<script>
export default {
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
                        prae: 'Prävention',
                        system: 'System',
                    },
                    active: 'system',
                },
            },
        };
    },

    methods: {},

    props: {
        data: {},
    },

    components: {
        box: () => import(/* webpackChunkName: "member" */ './Box'),
        stamm: () => import(/* webpackChunkName: "member" */ './boxes/Stamm'),
        kontakt: () => import(/* webpackChunkName: "member" */ './boxes/Kontakt'),
        prae: () => import(/* webpackChunkName: "member" */ './boxes/Prae'),
        courses: () => import(/* webpackChunkName: "member" */ './boxes/Courses'),
        system: () => import(/* webpackChunkName: "member" */ './boxes/System'),
        payments: () => import(/* webpackChunkName: "member" */ './boxes/Payments'),
        memberships: () => import(/* webpackChunkName: "member" */ './boxes/Memberships'),
        vmap: () => import(/* webpackChunkName: "member" */ './boxes/Vmap'),
        tabs: () => import(/* webpackChunkName: "member" */ './Tabs'),
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
        'praesystem';
    grid-template-columns: 1fr;
}
@media screen and (min-width: 1536px) {
    .this-grid {
        grid-template-areas:
            'stamm kontakt praev system'
            'courses courses memberships memberships'
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
</style>
