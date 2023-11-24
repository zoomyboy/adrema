<template>
    <page-layout>
        <template #toolbar>
            <page-toolbar-button :href="meta.links.index" color="primary" icon="undo">zurück</page-toolbar-button>
            <page-toolbar-button v-if="mode === 'edit'" :href="data.links.show" color="primary"
                icon="eye">anschauen</page-toolbar-button>
        </template>
        <template #right>
            <f-save-button form="memberedit"></f-save-button>
        </template>
        <form id="memberedit" class="flex grow relative" @submit.prevent="submit">
            <ui-popup v-if="conflict === true" heading="Ein Konflikt ist aufgetreten">
                <div>
                    <p class="mt-4">Dieses Mitglied wurde vorher bereits aktualisiert. Daher könnte ein Update zu
                        Datenverlust führen.</p>
                    <p class="mt-2">Wir empfehlen, die Daten aus NaMi zunächst neu zu synchronisieren und dann die
                        Änderungen hier in der Adrema erneut vorzunehmen.</p>
                    <div class="grid grid-cols-2 gap-3 mt-6">
                        <a href="#" class="text-center btn btn-primary" @click.prevent="resync">Neu synchronisieren</a>
                        <a href="#" class="text-center btn btn-danger" @click.prevent="forceWrite">Änderungen schreiben</a>
                    </div>
                </div>
            </ui-popup>

            <!-- ***************************** Hauptbereich ****************************** -->
            <div class="grow grid md:grid-cols-2 gap-3 p-3">
                <ui-box heading="Stammdaten">
                    <div class="grid sm:grid-cols-2 gap-3">
                        <div class="grid grid-cols-2 gap-3">
                            <f-select id="gender_id" v-model="inner.gender_id" name="gender_id" :options="meta.genders"
                                label="Geschlecht" size="sm"></f-select>
                            <f-text id="salutation" v-model="inner.salutation" size="sm" label="Anrede"></f-text>
                        </div>
                        <f-select id="nationality_id" v-model="inner.nationality_id" :options="meta.nationalities"
                            label="Staatsangehörigkeit" name="nationality_id" size="sm"></f-select>
                        <f-text id="firstname" v-model="inner.firstname" size="sm" label="Vorname" required></f-text>
                        <f-text id="lastname" v-model="inner.lastname" size="sm" label="Nachname" required></f-text>
                        <f-text id="address" v-model="inner.address" size="sm" label="Adresse"></f-text>
                        <f-text id="further_address" v-model="inner.further_address" size="sm"
                            label="Adresszusatz"></f-text>
                        <f-text id="zip" v-model="inner.zip" size="sm" label="PLZ"></f-text>
                        <f-text id="location" v-model="inner.location" size="sm" label="Ort"></f-text>
                        <f-text id="birthday" v-model="inner.birthday" type="date" size="sm" label="Geburtsdatum"></f-text>
                        <f-select id="region_id" v-model="inner.region_id" :options="meta.regions" name="region_id"
                            label="Bundesland" size="sm"></f-select>
                        <f-select id="country_id" v-model="inner.country_id" :options="meta.countries" label="Land"
                            name="country_id" size="sm" required></f-select>
                        <f-text id="other_country" v-model="inner.other_country" label="Andere Staatsangehörigkeit"
                            size="sm"></f-text>
                    </div>
                </ui-box>
                <ui-box heading="Kontakt">
                    <div class="grid gap-3 sm:grid-cols-2">
                        <f-text id="main_phone" v-model="inner.main_phone" size="sm" label="Telefon (Eltern)"></f-text>
                        <f-text id="mobile_phone" v-model="inner.mobile_phone" size="sm" label="Handy (Eltern)"></f-text>
                        <f-text id="work_phone" v-model="inner.work_phone" size="sm"
                            label="Tel geschäftlich (Eltern)"></f-text>
                        <f-text id="children_phone" v-model="inner.children_phone" size="sm"
                            label="Telefon (Kind)"></f-text>
                        <f-text id="email" v-model="inner.email" size="sm" label="E-Mail"></f-text>
                        <f-text id="email_parents" v-model="inner.email_parents" size="sm" label="E-Mail eltern"></f-text>
                        <f-text id="fax" v-model="inner.fax" size="sm" label="Fax"></f-text>
                        <f-textarea id="letter_address" v-model="inner.letter_address" class="sm:col-span-2" rows="3"
                            label="Brief-Adresse" size="sm"></f-textarea>
                    </div>
                </ui-box>
                <ui-box heading="System">
                    <div class="grid gap-3">
                        <f-select id="bill_kind" v-model="inner.bill_kind" :options="meta.billKinds"
                            label="Rechnung versenden über" name="bill_kind" size="sm"></f-select>
                        <f-select id="subscription_id" v-model="inner.subscription_id" :options="meta.subscriptions"
                            label="Beitrag" name="subscription_id" size="sm"></f-select>
                        <f-switch id="has_nami" v-model="inner.has_nami" size="sm" label="In Nami eintragen"></f-switch>
                        <f-switch id="send_newspaper" v-model="inner.send_newspaper" label="Mittendrin versenden"
                            size="sm"></f-switch>
                        <f-text id="joined_at" v-model="inner.joined_at" class="sm:col-span-2" type="date"
                            label="Eintrittsdatum" size="sm" required></f-text>
                        <f-textarea id="comment" v-model="inner.comment" rows="3" class="col-span-2" label="Kommentar"
                            size="sm"></f-textarea>
                        <div v-if="mode === 'create'" class="contents">
                            <f-select id="first_activity_id" v-model="inner.first_activity_id"
                                :options="meta.formCreateActivities" label="Erste Tätigkeit" name="first_activity_id"
                                size="sm" required></f-select>
                            <f-select v-if="inner.first_activity_id" id="first_subactivity_id"
                                v-model="inner.first_subactivity_id"
                                :options="meta.formCreateSubactivities[inner.first_activity_id]"
                                label="Erste Untertätigkeit" name="first_subactivity_id" size="sm" required></f-select>
                        </div>
                    </div>
                </ui-box>
                <ui-box heading="Prävention">
                    <div class="grid sm:grid-cols-[minmax(min-content,max-content)_minmax(min-content,max-content)] gap-2">
                        <div class="grid grid-cols-[minmax(min-content,max-content)_8rem] gap-1">
                            <f-switch id="has_efz" v-model="hasEfz" size="sm" label="Führungszeugnis eingesehen"></f-switch>
                            <div>
                                <f-text v-if="inner.efz !== null" id="efz" v-model="inner.efz" type="date" label="am"
                                    size="sm"></f-text>
                            </div>
                            <f-switch id="has_ps" v-model="hasPs" size="sm" label="Hat Präventionsschulung"></f-switch>
                            <div>
                                <f-text v-if="inner.ps_at !== null" id="ps_at" v-model="inner.ps_at" type="date" label="am"
                                    size="sm"></f-text>
                            </div>
                            <f-switch id="has_more_ps" v-model="hasMorePs" size="sm"
                                label="Hat Vertiefungsschulung"></f-switch>
                            <div>
                                <f-text v-if="inner.more_ps_at !== null" id="more_ps_at" v-model="inner.more_ps_at"
                                    type="date" label="am" size="sm"></f-text>
                            </div>
                            <f-switch id="is_recertified" v-model="isRecertified" size="sm"
                                label="Hat Rezertifizierung"></f-switch>
                            <div>
                                <f-text v-if="inner.recertified_at !== null" id="recertified_at"
                                    v-model="inner.recertified_at" type="date" label="am" size="sm"></f-text>
                            </div>
                            <f-switch id="has_without_education" v-model="hasWithoutEducation" label="Einsatz ohne Schulung"
                                size="sm"></f-switch>
                            <div>
                                <f-text v-if="inner.without_education_at !== null" id="without_education_at"
                                    v-model="inner.without_education_at" type="date" label="am" size="sm"></f-text>
                            </div>
                            <f-switch id="has_without_efz" v-model="hasWithoutEfz" size="sm"
                                label="Einsatz ohne EFZ"></f-switch>
                            <div>
                                <f-text v-if="inner.without_efz_at !== null" id="without_efz_at"
                                    v-model="inner.without_efz_at" type="date" label="am" size="sm"></f-text>
                            </div>
                        </div>
                        <div class="grid gap-1">
                            <f-switch id="has_svk" v-model="inner.has_svk" size="sm" label="SVK unterschrieben"></f-switch>
                            <f-switch id="has_vk" v-model="inner.has_vk" size="sm"
                                label="Verhaltenskodex unterschrieben"></f-switch>
                            <f-switch id="multiply_pv" v-model="inner.multiply_pv"
                                label="Multiplikator*in Präventionsschulung" size="sm"></f-switch>
                            <f-switch id="multiply_more_pv" v-model="inner.multiply_more_pv"
                                label="Multiplikator*in Vertiefungsschulung" size="sm"></f-switch>
                        </div>
                    </div>
                </ui-box>
            </div>
        </form>
    </page-layout>
</template>

<script>
function issetComputed(val) {
    return {
        set(v) {
            this.inner[val] = v ? '' : null;
        },
        get() {
            return this.inner[val] !== null;
        },
    };
}

export default {
    props: {
        mode: {},
        data: {},
        conflict: {},
        meta: {},
    },
    data: function () {
        return {
            inner: { ...this.data },
            active: 0,
        };
    },

    methods: {
        submit() {
            this.mode === 'create' ? this.$inertia.post(`/member`, this.inner) : this.$inertia.patch(`/member/${this.inner.id}`, this.inner);
        },
        resync() {
            this.$inertia.get(`/member/${this.inner.id}/resync`);
        },
        forceWrite() { },
    },

    computed: {
        hasEfz: issetComputed('efz'),
        hasPs: issetComputed('ps_at'),
        hasMorePs: issetComputed('more_ps_at'),
        isRecertified: issetComputed('recertified_at'),
        hasWithoutEfz: issetComputed('without_efz_at'),
        hasWithoutEducation: issetComputed('without_education_at'),
    },
};
</script>
