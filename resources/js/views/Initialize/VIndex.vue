<template>
    <div>
        <div v-if="step === 0">
            <full-page-heading>Willkommen im Adrema-Setup.<br /></full-page-heading>
            <div class="prose prose-invert">
                <p>Bitte gib deine NaMi-Zugangsdaten ein,<br />um eine erste Synchronisation durchzuführen.</p>
            </div>
            <form @submit.prevent="check" class="grid gap-3 mt-5">
                <f-text v-model="values.mglnr" label="Mitgliedsnummer" name="mglnr" id="mglnr" type="tel" required></f-text>
                <f-text v-model="values.password" type="password" label="Passwort" name="password" id="password" required></f-text>
                <ui-button class="mt-6" :is-loading="loading" type="submit">Weiter</ui-button>
            </form>
        </div>
        <div v-if="step === 1" class="grid grid-cols-5 w-full gap-3">
            <full-page-heading class="col-span-full !mb-0">Suchkriterien festlegen</full-page-heading>
            <form @submit.prevent="storeSearch" class="border-2 border-primary-800 border-solid p-3 rounded-lg grid gap-3 col-span-2">
                <div class="prose prose-invert max-w-none col-span-full">
                    <p>
                        Lege hier die Suchkriterien für den Abruf der Mitglieder-Daten fest. Mit diesen Suchkriterien wird im Anschluss eine Mitgliedersuche in NaMi durchgeführt. Alle Mitglieder, die
                        dann dort auftauchen werden in die Adrema übernommen. Dir wird hier eine Vorschau eingeblendet, damit du sicherstellen kannst, dass die Suchkriterien die richtigen sind.
                    </p>
                </div>
                <f-select
                    v-model="values.params.gruppierung1Id"
                    label="Diözesan-Gruppierung"
                    name="gruppierung1Id"
                    id="gruppierung1Id"
                    size="sm"
                    :options="searchLayerOptions[0]"
                    @input="loadSearchLayer(1, $event, search)"
                    hint="Gruppierungs-Nummer einer Diözese, auf die die Mitglieder passen sollen. I.d.R. ist das die Gruppierungsnummer deiner Diözese. Entspricht dem Feld '1. Ebene' in der NaMi Suche."
                ></f-select>
                <f-select
                    v-model="values.params.gruppierung2Id"
                    label="Bezirks-Gruppierung"
                    name="gruppierung2Id"
                    id="gruppierung2Id"
                    hint="Gruppierungs-Nummer eines Bezirks, auf die die Mitglieder passen sollen. I.d.R. ist das die Gruppierungsnummer deines Bezirks. Entspricht dem Feld '2. Ebene' in der NaMi Suche. Fülle dieses Feld aus, um Mitglieder auf einen bestimmten Bezirk zu begrenzen."
                    :disabled="!values.params.gruppierung1Id"
                    @input="loadSearchLayer(2, $event, search)"
                    size="sm"
                    :options="searchLayerOptions[1]"
                ></f-select>
                <f-select
                    v-model="values.params.gruppierung3Id"
                    label="Stammes-Gruppierung"
                    name="gruppierung3Id"
                    id="gruppierung3Id"
                    size="sm"
                    @input="search"
                    hint="Gruppierungs-Nummer deines Stammes, auf die die Mitglieder passen sollen. I.d.R. ist das die Gruppierungsnummer deines Stammes. Entspricht dem Feld '3. Ebene' in der NaMi Suche. Fülle dieses Feld aus, um Mitglieder auf einen bestimmten Stamm zu beschränken."
                    :disabled="!values.params.gruppierung1Id || !values.params.gruppierung2Id"
                    :options="searchLayerOptions[2]"
                ></f-select>
                <f-select
                    v-model="values.params.mglStatusId"
                    label="Mitglieds-Status"
                    name="mglStatusId"
                    id="mglStatusId"
                    size="sm"
                    @input="search"
                    :options="states"
                    hint="Wähle hier etwas aus, um nur aktive oder nur inaktive Mitglieder zu synchronisieren. Wir empfehlen dir, dies so zu belassen und Mitglieder ohne 'Datenweiterverwendung' gänzlich zu löschen, um Karteileichen zu entfernen."
                ></f-select>
                <f-switch
                    v-model="values.params.inGrp"
                    label="In Gruppierung suchen"
                    name="inGrp"
                    id="inGrp"
                    @input="search"
                    hint="Mitglieder finden, die direktes Mitglied in der kleinsten befüllten Gruppierung sind."
                    size="sm"
                ></f-switch>
                <f-switch
                    v-model="values.params.unterhalbGrp"
                    label="Unterhalb Gruppierung suchen"
                    name="unterhalbGrp"
                    id="unterhalbGrp"
                    @input="search"
                    hint="Mitglieder finden, die direktes Mitglied in einer Untergruppe der kleinsten befüllten Gruppierung sind."
                    size="sm"
                ></f-switch>
                <div class="col-span-full flex justify-center">
                    <ui-button :is-loading="loading" class="px-10" type="submit">Weiter</ui-button>
                </div>
            </form>

            <section class="col-span-3 text-sm col-span-3" v-if="preview !== null && preview.data.length">
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
                    <v-pages class="mt-4" :value="preview" @reload="reloadPage"></v-pages>
                </div>
            </section>
            <section class="col-span-3 items-center justify-center flex text-xl text-gray-200 border-2 border-primary-800 border-solid p-3 rounded-lg mt-4" v-else>Keine Mitglieder gefunden</section>
        </div>
        <div v-if="step === 2">
            <full-page-heading>Standard-Gruppierung</full-page-heading>
            <div class="prose prose-invert">
                <p>Bitte gib hier deine Standard-Gruppierungsnummer ein.</p>
                <p>Dieser Gruppierung werden Mitglieder automatisch zugeordnet,<br />falls nichts anderes angegeben wurde.</p>
                <p>I.d.R. ist das z.B. die Nummer deines Stammes, wenn du als StaVo mit Adrema Daten verwaltest.</p>
            </div>
            <form @submit.prevent="submit" class="grid gap-3 mt-5">
                <f-text v-model="values.group_id" label="Gruppierungs-Nummer" name="groupId" id="groupId" type="tel" required></f-text>
                <button type="submit" class="btn w-full btn-primary mt-6 inline-block">Weiter</button>
            </form>
        </div>
        <div v-if="step === 3">
            <full-page-heading>Einrichtung abgeschlossen</full-page-heading>
            <div class="prose prose-invert">
                <p>Wir werden nun die Mitgliederdaten anhand deiner festgelegten Kriterien abrufen.</p>
                <p>Per Klick auf "Abschließen" gelangst du zum Dashboard</p>
                <p>Viel Spaß mit Adrema</p>
            </div>
            <a href="/" class="mt-5 inline-block btn btn-primary">Abschließen</a>
        </div>
    </div>
</template>

<script>
import InstallLayout from '../../layouts/InstallLayout.vue';
import hasFlash from '../../mixins/hasFlash.js';
import debounce from 'lodash/debounce';

export default {
    layout: InstallLayout,

    mixins: [hasFlash],

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
            try {
                await this.axios.post('/initialize', this.values);
                this.step = 3;
            } catch (e) {
                this.errorsFromException(e);
            }
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
                await this.loadSearchResult(1);
                await this.loadSearchLayer(0, null, () => '');
                this.step = 1;
            } catch (e) {
                this.errorsFromException(e);
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
            } catch (e) {
                this.errorsFromException(e);
            } finally {
                this.loading = false;
            }
        },
        async loadSearchResult(page) {
            this.loading = true;
            try {
                var result = await this.axios.post('/nami/search', {...this.values, page: page});
                this.preview = result.data;
            } catch (e) {
                this.errorsFromException(e);
            } finally {
                this.loading = false;
            }
        },
    },
};
</script>
