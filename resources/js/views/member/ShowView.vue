<template>
    <div class="p-3 grid gap-3 this-grid grow">
        <box heading="Stammdaten" class="area-stamm hidden xl:block">
            <stamm :inner="inner"></stamm>
        </box>
        <box heading="Kontakt" class="area-kontakt hidden xl:block">
            <kontakt :inner="inner"></kontakt>
        </box>
        <box class="area-stammkontakt block xl:hidden">
            <tabs v-model="tabs.stammkontakt">
                <stamm v-show="tabs.stammkontakt.active === 'stamm'" :inner="inner"></stamm>
                <kontakt v-show="tabs.stammkontakt.active === 'kontakt'" :inner="inner"></kontakt>
            </tabs>
        </box>

        <box container-class="" heading="Prävention" class="area-praev hidden xl:block">
            <prae :inner="inner"></prae>
        </box>
        <box heading="System" class="area-system hidden xl:block">
            <system :inner="inner"></system>
        </box>
        <box class="area-praesystem block xl:hidden">
            <tabs v-model="tabs.praesystem">
                <prae v-show="tabs.praesystem.active === 'prae'" :inner="inner"></prae>
                <system v-show="tabs.praesystem.active === 'system'" :inner="inner"></system>
            </tabs>
        </box>

        <box class="area-membershipcourse hidden xl:block">
            <tabs v-model="tabs.membershipcourse">
                <courses v-show="tabs.membershipcourse.active === 'course'" :value="inner.courses"></courses>
                <memberships
                    v-show="tabs.membershipcourse.active === 'membership'"
                    :value="inner.memberships"
                ></memberships>
            </tabs>
        </box>
        <box heading="Ausbildungen" class="area-courses xl:hidden">
            <courses :value="inner.courses"></courses>
        </box>
        <box heading="Mitgliedschaften" class="area-memberships xl:hidden">
            <memberships :value="inner.memberships"></memberships>
        </box>

        <box heading="Zahlungen" class="area-payments">
            <payments :value="inner.payments"></payments>
        </box>

        <box heading="Karte" container-class="grow" class="area-map hidden xl:block">
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
                        system: 'System',
                        prae: 'Prävention',
                    },
                    active: 'system',
                },
                membershipcourse: {
                    children: {
                        membership: 'Mitgliedshaften',
                        course: 'Ausbildungen',
                    },
                    active: 'membership',
                },
            },
        };
    },

    methods: {},

    props: {
        data: {},
    },

    components: {
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
