<template>
    <page-full-layout>
        <div v-if="step === 0">
            <page-full-heading>Willkommen im Adrema-Setup.<br /></page-full-heading>
            <div class="prose prose-invert">
                <p>Bitte gib deine NaMi-Zugangsdaten ein,<br />um eine erste Synchronisation durchzuführen.</p>
            </div>
            <form class="grid gap-3 mt-5" @submit.prevent="check">
                <f-text id="mglnr" v-model="values.mglnr" label="Mitgliedsnummer" type="tel" required></f-text>
                <f-text id="password" v-model="values.password" type="password" label="Passwort" required></f-text>
                <ui-button class="mt-6" :is-loading="loading" type="submit">Weiter</ui-button>
            </form>
        </div>
        <div v-if="step === 1" class="grid grid-cols-5 w-full gap-3">
            <page-full-heading class="col-span-full !mb-0">Suchkriterien festlegen</page-full-heading>
            <form class="border-2 border-primary-800 border-solid p-3 rounded-lg grid gap-3 col-span-2" @submit.prevent="storeSearch">
                <div class="prose prose-invert max-w-none col-span-full">
                    <p>
                        Lege hier die Suchkriterien für den Abruf der Mitglieder-Daten fest. Mit diesen Suchkriterien wird im Anschluss eine Mitgliedersuche in NaMi durchgeführt. Alle Mitglieder, die
                        dann dort auftauchen werden in die Adrema übernommen. Dir wird hier eine Vorschau eingeblendet, damit du sicherstellen kannst, dass die Suchkriterien die richtigen sind.
                    </p>
                </div>
                <f-select
                    id="gruppierung1Id"
                    v-model="values.params.gruppierung1Id"
                    label="Diözesan-Gruppierung"
                    name="gruppierung1Id"
                    size="sm"
                    :options="searchLayerOptions[0]"
                    hint="Gruppierungs-Nummer einer Diözese, auf die die Mitglieder passen sollen. I.d.R. ist das die Gruppierungsnummer deiner Diözese. Entspricht dem Feld '1. Ebene' in der NaMi Suche."
                    @update:modelValue="loadSearchLayer(1, $event, search)"
                ></f-select>
                <f-select
                    id="gruppierung2Id"
                    v-model="values.params.gruppierung2Id"
                    label="Bezirks-Gruppierung"
                    name="gruppierung2Id"
                    hint="Gruppierungs-Nummer eines Bezirks, auf die die Mitglieder passen sollen. I.d.R. ist das die Gruppierungsnummer deines Bezirks. Entspricht dem Feld '2. Ebene' in der NaMi Suche. Fülle dieses Feld aus, um Mitglieder auf einen bestimmten Bezirk zu begrenzen."
                    :disabled="!values.params.gruppierung1Id"
                    size="sm"
                    :options="searchLayerOptions[1]"
                    @update:modelValue="loadSearchLayer(2, $event, search)"
                ></f-select>
                <f-select
                    id="gruppierung3Id"
                    v-model="values.params.gruppierung3Id"
                    label="Stammes-Gruppierung"
                    name="gruppierung3Id"
                    size="sm"
                    hint="Gruppierungs-Nummer deines Stammes, auf die die Mitglieder passen sollen. I.d.R. ist das die Gruppierungsnummer deines Stammes. Entspricht dem Feld '3. Ebene' in der NaMi Suche. Fülle dieses Feld aus, um Mitglieder auf einen bestimmten Stamm zu beschränken."
                    :disabled="!values.params.gruppierung1Id || !values.params.gruppierung2Id"
                    :options="searchLayerOptions[2]"
                    @update:modelValue="search"
                ></f-select>
                <f-select
                    id="mglStatusId"
                    v-model="values.params.mglStatusId"
                    label="Mitglieds-Status"
                    name="mglStatusId"
                    size="sm"
                    :options="states"
                    hint="Wähle hier etwas aus, um nur aktive oder nur inaktive Mitglieder zu synchronisieren. Wir empfehlen dir, dies so zu belassen und Mitglieder ohne 'Datenweiterverwendung' gänzlich zu löschen, um Karteileichen zu entfernen."
                    @update:modelValue="search"
                ></f-select>
                <f-switch
                    id="inGrp"
                    v-model="values.params.inGrp"
                    label="In Gruppierung suchen"
                    name="inGrp"
                    hint="Mitglieder finden, die direktes Mitglied in der kleinsten befüllten Gruppierung sind."
                    size="sm"
                    @update:modelValue="search"
                ></f-switch>
                <f-switch
                    id="unterhalbGrp"
                    v-model="values.params.unterhalbGrp"
                    label="Unterhalb Gruppierung suchen"
                    name="unterhalbGrp"
                    hint="Mitglieder finden, die direktes Mitglied in einer Untergruppe der kleinsten befüllten Gruppierung sind."
                    size="sm"
                    @update:modelValue="search"
                ></f-switch>
                <div class="col-span-full flex justify-center">
                    <ui-button :is-loading="loading" class="!px-10" type="submit">Weiter</ui-button>
                </div>
            </form>

            <section v-if="preview !== null && preview.data.length" class="col-span-3 text-sm col-span-3">
                <table cellspacing="0" cellpadding="0" border="0" class="custom-table custom-table-sm hidden md:table">
                    <thead>
                        <th>GruppierungsNr</th>
                        <th>MitgliedsNr</th>
                        <th>Nachname</th>
                        <th>Vorname</th>
                        <th>Geburtsdatum</th>
                    </thead>

                    <tr v-for="(member, index) in preview.data" :key="index">
                        <td v-text="member.groupId"></td>
                        <td v-text="member.memberId"></td>
                        <td v-text="member.lastname"></td>
                        <td v-text="member.firstname"></td>
                        <td v-text="member.birthday_human"></td>
                    </tr>
                </table>

                <div v-if="preview !== null" class="px-6">
                    <ui-pagination class="mt-4" :value="preview" @reload="reloadPage"></ui-pagination>
                </div>
            </section>
            <section v-else class="col-span-3 items-center justify-center flex text-xl text-gray-200 border-2 border-primary-800 border-solid p-3 rounded-lg mt-4">Keine Mitglieder gefunden</section>
        </div>
        <div v-if="step === 2">
            <page-full-heading>Standard-Gruppierung</page-full-heading>
            <div class="prose prose-invert">
                <p>Bitte gib hier deine Standard-Gruppierungsnummer ein.</p>
                <p>Dieser Gruppierung werden Mitglieder automatisch zugeordnet,<br />falls nichts anderes angegeben wurde.</p>
                <p>I.d.R. ist das z.B. die Nummer deines Stammes, wenn du als StaVo mit Adrema Daten verwaltest.</p>
            </div>
            <form class="grid grid-cols-2 gap-3 mt-5" @submit.prevent="submit">
                <f-text id="groupId" v-model="values.group_id" label="Gruppierungs-Nummer" type="tel" class="col-span-full" required></f-text>
                <ui-button class="btn-secondary" @click.prevent="step--">Zurück</ui-button>
                <ui-button type="submit">Weiter</ui-button>
            </form>
        </div>
        <div v-if="step === 3">
            <page-full-heading>Einrichtung abgeschlossen</page-full-heading>
            <div class="prose prose-invert">
                <p>Wir werden nun die Mitgliederdaten anhand deiner festgelegten Kriterien abrufen.</p>
                <p>Per Klick auf "Abschließen" gelangst du zum Dashboard</p>
                <p>Viel Spaß mit Adrema</p>
            </div>
            <a href="/" class="mt-5 inline-block btn btn-primary">Abschließen</a>
        </div>
    </page-full-layout>
</template>

<script>
import FullLayout from '../../layouts/FullLayout.vue';
import hasFlash from '../../mixins/hasFlash.js';
import debounce from 'lodash/debounce';

export default {

    mixins: [hasFlash],
    layout: FullLayout,

    data: function () {
        return {
            searchLayerOptions: [[], [], []],
            loading: false,
            preview: null,
            states: [
                {id: 'INAKTIV', name: 'Inaktiv'},
                {id: 'AKTIV', name: 'Aktiv'},
            ],
            step: 0,
            values: {
                mglnr: '',
                password: '',
                group_id: '',
                params: {
                    mglStatusId: 'AKTIV',
                    gruppierung1Id: '',
                    gruppierung2Id: '',
                    gruppierung3Id: '',
                    inGrp: true,
                    unterhalbGrp: true,
                },
            },
        };
    },
    methods: {
        async submit() {
            await this.axios.post('/initialize', this.values);
            this.step = 3;
        },
        async storeSearch() {
            this.values.group_id = this.values.params.gruppierung3Id ? this.values.params.gruppierung3Id : this.values.params.gruppierung2Id;
            this.step = 2;
        },
        async reloadPage(page) {
            await this.loadSearchResult(page);
        },
        async check() {
            this.loading = true;
            try {
                await this.axios.post('/nami/login-check', this.values);
                this.$success('Login erfolgreich');
                await this.loadSearchResult(1);
                await this.loadSearchLayer(0, null, () => '');
                this.step = 1;
            } finally {
                this.loading = false;
            }
        },
        search: debounce(async function () {
            await this.loadSearchResult(1);
        }, 500),
        async loadSearchLayer(parentLayer, parent, after) {
            this.loading = true;
            try {
                var result = await this.axios.post('/nami/get-search-layer', {...this.values, layer: parentLayer, parent});

                this.searchLayerOptions = this.searchLayerOptions.map((layers, index) => {
                    if (index < parentLayer) {
                        return layers;
                    }

                    var groupIndex = index + 1;
                    this.values.params[`gruppierung${groupIndex}Id`] = null;

                    if (index === parentLayer) {
                        return result.data;
                    }

                    return [];
                });

                after();
            } finally {
                this.loading = false;
            }
        },
        async loadSearchResult(page) {
            this.loading = true;
            try {
                var result = await this.axios.post('/nami/search', {...this.values, page: page});
                this.preview = result.data;
            } finally {
                this.loading = false;
            }
        },
    },
};
</script>
