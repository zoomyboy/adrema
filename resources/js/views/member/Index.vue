<template>
    <div>
        <div class="member-table">
            <header>
                <div class="px-6 text-gray-200 font-semibold py-3 border-gray-600 border-b">Nachname</div>
                <div class="px-6 text-gray-200 font-semibold py-3 border-gray-600 border-b">Vorname</div>
                <div class="px-6 text-gray-200 font-semibold py-3 border-gray-600 border-b">Straße</div>
                <div class="px-6 text-gray-200 font-semibold py-3 border-gray-600 border-b">PLZ</div>
                <div class="px-6 text-gray-200 font-semibold py-3 border-gray-600 border-b">Ort</div>
                <div class="px-6 text-gray-200 font-semibold py-3 border-gray-600 border-b">Mittendrin</div>
                <div class="px-6 text-gray-200 font-semibold py-3 border-gray-600 border-b">Nami</div>
                <div class="px-6 text-gray-200 font-semibold py-3 border-gray-600 border-b">Rechnung</div>
                <div class="px-6 text-gray-200 font-semibold py-3 border-gray-600 border-b">Geburtstag</div>
                <div class="px-6 text-gray-200 font-semibold py-3 border-gray-600 border-b">Eintritt</div>
            </header>

            <inertia-link :href="`/member/${member.id}/edit`" :key="index" v-for="member, index in data.data" class="text-gray-200 transition-all duration-500 rounded flex hover:bg-gray-600">
                <div class="py-1 px-6" v-text="member.firstname"></div>
                <div class="py-1 px-6" v-text="member.lastname"></div>
                <div class="py-1 px-6" v-text="`${member.address}`"></div>
                <div class="py-1 px-6" v-text="`${member.zip}`"></div>
                <div class="py-1 px-6" v-text="`${member.location}`"></div>
                <div class="py-1 px-6">
                    <v-bool v-model="member.send_newspaper"></v-bool>
                </div>
                <div class="py-1 px-6">
                    <v-bool v-model="member.has_nami"></v-bool>
                </div>
                <div class="py-1 px-6">
                    <div class="py-1 rounded-full flex text-xs items-center justify-center leading-none bg-primary-900" v-text="member.bill_kind_name" v-if="member.bill_kind_name"></div>
                    <div class="py-1 rounded-full flex text-xs items-center justify-center leading-none" v-else>Kein</div>
                </div>
                <div class="py-1 px-6" v-text="`${member.birthday_human}`"></div>
                <div class="py-1 px-6" v-text="`${member.joined_at_human}`"></div>
            </inertia-link>

        </div>

        <div class="px-6">
            <pages class="mt-4" :value="data.meta" :only="['data']"></pages>
        </div>
    </div>
</template>

<script>
import App from '../../layouts/App';

export default {
    layout: App,

    props:{
        data: {}
    }
}
</script>

<style scoped>
.member-table {
    display: table;
    width: 100%;
}
.member-table > * {
    display: table-row;
}
.member-table > * > * {
    display: table-cell;
}
</style>
