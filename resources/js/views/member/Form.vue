<template>
    <form class="flex grow relative" @submit.prevent="submit">
        <!-- ****************************** menu links ******************************* -->
        <div class="p-6 bg-gray-700 border-r border-gray-600 flex-none w-maxc flex flex-col justify-between">
            <div class="grid gap-1">
                <a v-for="item, index in menu" :key="index" href="#" :data-cy="`menu-${item.id}`" @click.prevent="openMenu(index)" class="rounded py-1 px-3 text-gray-400" :class="index == active ? `bg-gray-600` : ''" v-text="item.title"></a>
            </div>
            <div>
                <button type="button" v-show="mode !== 'create'" @click.prevent="confirm" class="btn block w-full btn-primary">Daten bestätigen</button>
                <button type="submit" class="mt-3 btn block w-full btn-primary">Speichern</button>
            </div>
        </div>

        <!-- ***************************** Hauptbereich ****************************** -->
        <div class="grow">
            <div class="grid grid-cols-2 gap-3 p-4" v-if="menuTitle == 'Stammdaten'">
                <f-select id="gender_id" :options="genders" v-model="inner.gender_id" label="Geschlecht"></f-select>
                <f-text id="firstname" v-model="inner.firstname" label="Vorname" required></f-text>
                <f-text id="lastname" v-model="inner.lastname" label="Nachname" required></f-text>
                <f-text id="address" v-model="inner.address" label="Adresse" required></f-text>
                <f-text id="further_address" v-model="inner.further_address" label="Adresszusatz"></f-text>
                <f-text id="zip" v-model="inner.zip" label="PLZ" required></f-text>
                <f-text id="location" v-model="inner.location" label="Ort" required></f-text>
                <f-text type="date" id="birthday" v-model="inner.birthday" label="Geburtsdatum" required></f-text>
                <f-select :options="regions" id="region_id" v-model="inner.region_id" label="Bundesland"></f-select>
                <f-select :options="countries" id="country_id" v-model="inner.country_id" label="Land" required></f-select>
                <f-select :options="nationalities" id="nationality_id" v-model="inner.nationality_id" label="Staatsangehörigkeit" required></f-select>

                <div class="contents">
                    <h2 class="col-span-full font-semibold text-lg text-white">Erste Gruppierung</h2>
                    <f-select :options="activities" id="first_activity_id" v-model="inner.first_activity_id" label="Erste Tätigkeit" required></f-select>
                    <f-select v-if="inner.first_activity_id" :options="subactivities[inner.first_activity_id]" id="first_subactivity_id" v-model="inner.first_subactivity_id" label="Erste Untertätigkeit" required></f-select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3 p-4" v-if="menuTitle == 'Kontakt'">
                <f-text id="main_phone" v-model="inner.main_phone" label="Telefon (Eltern)"></f-text>
                <f-text id="mobile_phone" v-model="inner.mobile_phone" label="Handy (Eltern)"></f-text>
                <f-text id="work_phone" v-model="inner.work_phone" label="Tel geschäftlich (Eltern)"></f-text>
                <f-text id="children_phone" v-model="inner.children_phone" label="Telefon (Kind)"></f-text>
                <f-text id="email" v-model="inner.email" label="E-Mail"></f-text>
                <f-text id="email_parents" v-model="inner.email_parents" label="E-Mail eltern"></f-text>
                <f-text id="fax" v-model="inner.fax" label="Fax"></f-text>
            </div>
            <div class="grid grid-cols-2 gap-3 p-4" v-if="menuTitle == 'System'">
                <f-select :options="billKinds" id="bill_kind_id" v-model="inner.bill_kind_id" label="Rechnung versenden über"></f-select>
            </div>
            <div class="grid grid-cols-4 gap-3 p-4" v-if="menuTitle == 'Sonstiges'">
                <f-text class="col-span-2" id="other_country" v-model="inner.other_country" label="Andere Staatsangehörigkeit"></f-text>
                <f-switch id="has_nami" v-model="inner.has_nami" label="In Nami eintragen"></f-switch>
                <f-switch id="send_newspaper" v-model="inner.send_newspaper" label="Mittendrin"></f-switch>
                <f-text class="col-span-2" type="date" id="joined_at" v-model="inner.joined_at" label="Eintrittsdatum"></f-text>
                <f-select class="col-span-2" :options="confessions" id="confession_id" v-model="inner.confession_id" label="Konfession"></f-select>
                <f-select class="col-span-2" :options="subscriptions" id="subscription_id" v-model="inner.subscription_id" label="Beitrag"></f-select>
                <f-textarea class="col-span-2" rows="4" id="letter_address" v-model="inner.letter_address" label="Brief-Adresse"></f-textarea>
            </div>
        </div>
    </form>
</template>

<script>
export default {
    data: function() {
        return {
            inner: {},
            active: 0,
            menu: [
                { id: 'stammdaten', title: 'Stammdaten' },
                { id: 'kontakt', title: 'Kontakt' },
                { id: 'system', title: 'System' },
                { id: 'sonstiges', title: 'Sonstiges' },
            ]
        };
    },

    props: {
        subactivities: {},
        activities: {},
        mode: {},
        genders: {},
        subscriptions: {},
        data: {},
        regions: {},
        countries: {},
        nationalities: {},
        confessions: {},
        billKinds: {},
    },

    methods: {
        confirm() {
            this.$inertia.post(`/member/${this.inner.id}/confirm`);
        },
        openMenu(index) {
            this.active = index;
        },
        submit() {
            this.mode === 'create'
                ? this.$inertia.post(`/member`, this.inner)
                : this.$inertia.patch(`/member/${this.inner.id}`, this.inner);
        }
    },

    computed: {
        menuTitle() {
            return this.menu[this.active].title;
        }
    },

    created() {
        this.inner = this.data;
    }
};
</script>
