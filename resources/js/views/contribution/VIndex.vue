<template>
    <page-layout>
        <form target="_BLANK" class="max-w-4xl w-full mx-auto gap-6 grid-cols-2 grid p-6">
            <f-text id="eventName" v-model="values.eventName" class="col-span-2" label="Veranstaltungs-Name" required />
            <f-text id="dateFrom" v-model="values.dateFrom" type="date" label="Datum von" required />
            <f-text id="dateUntil" v-model="values.dateUntil" type="date" label="Datum bis" required />

            <f-text id="zipLocation" v-model="values.zipLocation" label="PLZ / Ort" required />
            <f-select id="country" v-model="values.country" :options="countries" name="country" label="Land" required />

            <div class="border-gray-200 shadow shadow-primary-700 p-3 shadow-[0_0_4px_gray] col-span-2">
                <f-text id="search_text" ref="searchInput" v-model="searchString" class="col-span-2" label="Suchen …" size="sm" @keypress.enter.prevent="onSubmitFirstMemberResult" />
                <div class="mt-2 grid grid-cols-[repeat(auto-fill,minmax(180px,1fr))] gap-2 col-span-2">
                    <f-switch v-for="member in results.hits"
                              :id="`members-${member.id}`"
                              :key="member.id"
                              v-model="values.members"
                              :label="member.fullname"
                              name="members[]"
                              :value="member.id"
                              size="sm"
                              inline
                              @keypress.enter.prevent="onSubmitMemberResult(member)"
                    />
                </div>
            </div>

            <button v-for="(compiler, index) in compilers" :key="index" class="btn btn-primary mt-3 inline-block" @click.prevent="submit(compiler.class)" v-text="compiler.title" />
        </form>
    </page-layout>
</template>

<script lang="js" setup>
import { ref, inject } from 'vue';
import useSearch from '../../composables/useSearch.js';
const axios = inject('axios');

const { searchString, results, clearSearch } = useSearch(['birthday IS NOT NULL', 'address IS NOT EMPTY']);

const props = defineProps({
    data: {},
    countries: {},
    compilers: {},
});

const searchInput = ref([]);
const values = ref({
    type: null,
    members: [],
    eventName: '',
    dateFrom: '',
    dateUntil: '',
    zipLocation: '',
    country: null,
    ...props.data,
});

async function submit(compiler) {
    values.value.type = compiler;
    const payload = btoa(encodeURIComponent(JSON.stringify(values.value)));
    await axios.get(`/contribution-generate?payload=${payload}&validate=1`);
    window.open(`/contribution-generate?payload=${payload}`);
}
function onSubmitMemberResult(selected) {
    if (values.value.members.find((m) => m === selected.id) !== undefined) {
        values.value.members = values.value.members.filter((m) => m !== selected.id);
    } else {
        values.value.members.push(selected.id);
    }

    clearSearch();
    searchInput.value.$el.querySelector('input').focus();
}
function onSubmitFirstMemberResult() {
    if (results.value.hits.length === 0) {
        clearSearch();
        return;
    }

    onSubmitMemberResult(results.value.hits[0]);
}
</script>
