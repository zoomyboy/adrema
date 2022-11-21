<template>
    <div class="p-6 grid gap-6 this-grid grow">
        <!-- ****************************** Stammdaten ******************************* -->
        <box container-class="grid grid-cols-2 gap-3" heading="Stammdaten" class="area-stamm">
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
        </box>

        <!-- ******************************** Kontakt ******************************** -->
        <box container-class="grid gap-3" heading="Kontakt" class="area-kontakt">
            <key-value
                v-show="inner.main_phone"
                label="Telefon Eltern"
                :value="inner.main_phone"
                type="tel"
            ></key-value>
            <key-value
                v-show="inner.mobile_phone"
                label="Handy Eltern"
                :value="inner.mobile_phone"
                type="tel"
            ></key-value>
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
        </box>

        <!-- ****************************** Prävention ******************************* -->
        <box container-class="flex gap-3" heading="Prävention" class="area-praev">
            <div class="grid gap-3">
                <key-value
                    class="col-start-1"
                    label="Führungszeugnis eingesehen"
                    :value="inner.efz_human ? inner.efz_human : 'nie'"
                ></key-value>
                <key-value
                    class="col-start-1"
                    label="Präventionsschulung"
                    :value="inner.ps_at_human ? inner.ps_at_human : 'nie'"
                ></key-value>
                <key-value
                    class="col-start-1"
                    label="Vertiefungsschulung"
                    :value="inner.more_ps_at_human ? inner.more_ps_at_human : 'nie'"
                ></key-value>
                <key-value
                    class="col-start-1"
                    label="Einsatz ohne Schulung"
                    :value="inner.without_education_at_human ? inner.without_education_at_human : 'nie'"
                ></key-value>
                <key-value
                    class="col-start-1"
                    label="Einsatz ohne EFZ"
                    :value="inner.without_efz_at_human ? inner.without_efz_at_human : 'nie'"
                ></key-value>
            </div>
            <div class="grid gap-3 content-start">
                <boolean :value="inner.has_vk" long-label="Verhaltenskodex unterschrieben" label="VK"></boolean>
                <boolean :value="inner.has_svk" long-label="SVK unterschrieben" label="SVK"></boolean>
                <boolean
                    :value="inner.multiply_pv"
                    long-label="Multiplikator*in Präventionsschulung"
                    label="Multipl. PS"
                ></boolean>
                <boolean
                    :value="inner.multiply_more_pv"
                    long-label="Multiplikator*in Vertierungsschulung"
                    label="Multipl. VS"
                ></boolean>
            </div>
        </box>

        <!-- ******************************** Courses ******************************** -->
        <box heading="Ausbildungen" class="area-courses">
            <table
                cellspacing="0"
                cellpadding="0"
                border="0"
                class="custom-table custom-table-sm text-sm"
                v-if="inner.courses.length"
            >
                <thead>
                    <th>Datum</th>
                    <th>Baustein</th>
                    <th>Veranstaltung</th>
                    <th>Organisator</th>
                </thead>
                <tr v-for="(course, index) in inner.courses" :key="index">
                    <td v-text="course.completed_at_human"></td>
                    <td v-text="course.course.short_name"></td>
                    <td v-text="course.event_name"></td>
                    <td v-text="course.organizer"></td>
                </tr>
            </table>
            <div class="py-3 text-gray-400 text-center" v-else>Keine Ausbildungen vorhanden</div>
        </box>

        <!-- ******************************** System ********************************* -->
        <box container-class="grid gap-3" heading="System" class="area-system">
            <key-value v-show="inner.nami_id" label="Nami Mitgliedsnummer" :value="inner.nami_id"></key-value>
            <key-value label="Beitrag" :value="inner.subscription ? inner.subscription.name : 'kein'"></key-value>
            <key-value v-if="inner.joined_at_human" label="Eintrittsdatum" :value="inner.joined_at_human"></key-value>
            <key-value v-if="inner.bill_kind_name" label="Rechnung" :value="inner.bill_kind_name"></key-value>
            <boolean :value="inner.send_newspaper" label="Mittendrin versenden"></boolean>
        </box>

        <!-- *************************** Mitgliedschaften **************************** -->
        <box heading="Mitgliedschaften" class="area-memberships">
            <table cellspacing="0" cellpadding="0" border="0" class="custom-table custom-table-sm text-sm">
                <thead>
                    <th>Tätigkeit</th>
                    <th>Untertätigkeit</th>
                    <th>Datum</th>
                </thead>
                <tr v-for="(membership, index) in inner.memberships" :key="index">
                    <td v-text="membership.activity_name"></td>
                    <td v-text="membership.subactivity_name"></td>
                    <td v-text="membership.human_date"></td>
                </tr>
            </table>
        </box>

        <!-- ******************************* Zahlungen ******************************* -->
        <box heading="Zahlungen" class="area-payments">
            <table cellspacing="0" cellpadding="0" border="0" class="custom-table custom-table-sm text-sm">
                <thead>
                    <th>Nr</th>
                    <th>Status</th>
                    <th>Betrag-Name</th>
                    <th>Betrag</th>
                </thead>
                <tr v-for="(payment, index) in inner.payments" :key="index">
                    <td v-text="payment.nr"></td>
                    <td v-text="payment.status_name"></td>
                    <td v-text="payment.subscription.name"></td>
                    <td v-text="payment.subscription.amount_human"></td>
                </tr>
            </table>
        </box>

        <!-- ********************************* Karte ********************************* -->
        <box heading="Karte" container-class="grow" class="area-map">
            <iframe
                width="100%"
                height="100%"
                frameborder="0"
                scrolling="no"
                marginheight="0"
                marginwidth="0"
                src="https://www.openstreetmap.org/export/embed.html?bbox=9.699318408966066%2C47.484177893725764%2C9.729595184326174%2C47.49977861091604&amp;layer=mapnik&amp;marker=47.49197883161885%2C9.714467525482178"
                style="border: 1px solid black"
            >
            </iframe>
        </box>
    </div>
</template>

<script>
export default {
    data: function () {
        return {
            inner: {},
        };
    },

    methods: {},

    props: {
        data: {},
    },

    components: {
        'key-value': () => import('./KeyValue'),
        'boolean': () => import('./Boolean'),
        'box': () => import('./Box'),
    },

    created() {
        this.inner = this.data;
    },
};
</script>

<style scoped>
.this-grid {
    grid-template-areas:
        'stamm kontakt prae system'
        'courses courses memberships memberships'
        'payments payments map map';
    grid-template-columns: max-content max-content max-content 1fr;
}
.area-stamm {
    grid-area: stamm;
}
.area-kontakt {
    grid-area: kontakt;
}
.area-prae {
    grid-area: prae;
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
</style>
