<template>
    <form class="flex flex-grow relative" @submit.prevent="$inertia.patch(`/member/${inner.id}`, inner)">
        <!-- ****************************** menu links ******************************* -->
        <div class="p-6 bg-gray-700 border-r border-gray-600 flex-none w-maxc flex flex-col justify-between">
            <div class="grid gap-1">
                <a v-for="item, index in menu" :key="index" href="#" @click.prevent="openMenu(index)" class="rounded py-1 px-3 text-gray-400" :class="index == active ? `bg-gray-600` : ''" v-text="item.title"></a>
            </div>
            <div>
                <button type="submit" class="btn block w-full btn-primary">Speichern</button>
            </div>
        </div>

        <div class="flex-grow">
            <div class="grid grid-cols-2 gap-3 p-4" v-if="menuTitle == 'Stammdaten'">
                <div>
                    <f-select id="gender_id" :options="genders" v-model="inner.gender_id" label="Geschlecht"></f-select>
                </div>
                <div>
                    <f-text id="firstname" v-model="inner.firstname" label="Vorname" required></f-text>
                </div>
                <div>
                    <f-text id="lastname" v-model="inner.lastname" label="Nachname" required></f-text>
                </div>
                <div>
                    <f-text id="address" v-model="inner.address" label="Adresse" required></f-text>
                </div>
                <div>
                    <f-text id="further_address" v-model="inner.further_address" label="Adresszusatz"></f-text>
                </div>
                <div>
                    <f-text id="zip" v-model="inner.zip" label="PLZ" required></f-text>
                </div>
                <div>
                    <f-text id="location" v-model="inner.location" label="Ort" required></f-text>
                </div>
                <div>
                    <f-text type="date" id="birthday" v-model="inner.birthday" label="Geburtsdatum" required></f-text>
                </div>
                <div>
                    <f-select :options="regions" id="region_id" v-model="inner.region_id" label="Bundesland"></f-select>
                </div>
                <div>
                    <f-select :options="countries" id="country_id" v-model="inner.country_id" label="Land" required></f-select>
                </div>
                <div>
                    <f-select :options="nationalities" id="nationality_id" v-model="inner.nationality_id" label="Staatsangehörigkeit" required></f-select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3 p-4" v-if="menuTitle == 'Kontakt'">
                <div>
                    <f-text id="main_phone" v-model="inner.main_phone" label="Telefon"></f-text>
                </div>
                <div>
                    <f-text id="mobile_phone" v-model="inner.mobile_phone" label="Handy"></f-text>
                </div>
                <div>
                    <f-text id="work_phone" v-model="inner.work_phone" label="Tel geschäftlich"></f-text>
                </div>
                <div>
                    <f-text id="email" v-model="inner.email" label="E-Mail"></f-text>
                </div>
                <div>
                    <f-text id="email_parents" v-model="inner.email_parents" label="E-Mail eltern"></f-text>
                </div>
                <div>
                    <f-text id="fax" v-model="inner.fax" label="Fax"></f-text>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3 p-4" v-if="menuTitle == 'System'">
                <div>
                    <f-select :options="billKinds" id="bill_kind_id" v-model="inner.bill_kind_id" label="Rechnung versenden über"></f-select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3 p-4" v-if="menuTitle == 'Sonstiges'">
                <div>
                    <f-text id="other_country" v-model="inner.other_country" label="Andere Staatsangehörigkeit"></f-text>
                </div>
                <div>
                    <f-text type="date" id="joined_at" v-model="inner.joined_at" label="Eintrittsdatum"></f-text>
                </div>
                <div>
                    <f-select :options="confessions" id="confession_id" v-model="inner.confession_id" label="Konfession"></f-select>
                </div>
                <div>
                    <f-textarea rows="4" id="letter_address" v-model="inner.letter_address" label="Brief-Adresse"></f-textarea>
                </div>
            </div>
        </div>
    </form>
</template>

<script>
export default {
    data: function() {
        return {
            inner: {},
            active: 2,
            menu: [
                { title: 'Stammdaten' },
                { title: 'Kontakt' },
                { title: 'System' },
                { title: 'Sonstiges' },
            ]
        };
    },

    props: {
        genders: {},
        fees: {},
        data: {},
        regions: {},
        countries: {},
        nationalities: {},
        confessions: {},
        billKinds: {},
    },

    methods: {
        openMenu(index) {
            this.active = index;
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
