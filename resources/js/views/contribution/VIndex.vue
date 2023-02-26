<template>
    <form action="/contribution/generate" target="_BLANK" class="max-w-4xl w-full mx-auto gap-6 grid-cols-2 grid p-6">
        <f-text
            id="eventName"
            name="eventName"
            class="col-span-2"
            v-model="values.eventName"
            label="Veranstaltungs-Name"
            required
        ></f-text>
        <f-text id="dateFrom" name="dateFrom" type="date" v-model="values.dateFrom" label="Datum von" required></f-text>
        <f-text
            id="dateUntil"
            name="dateUntil"
            type="date"
            v-model="values.dateUntil"
            label="Datum bis"
            required
        ></f-text>

        <f-text id="zipLocation" name="zipLocation" v-model="values.zipLocation" label="PLZ / Ort" required></f-text>
        <f-select
            id="country"
            :options="countries"
            name="country"
            v-model="values.country"
            label="Land"
            required
        ></f-select>

        <div class="border-gray-200 shadow shadow-primary-700 p-3 shadow-[0_0_4px_gray] col-span-2">
            <f-text
                class="col-span-2"
                id="search_text"
                name="search_text"
                v-model="searchText"
                label="Suchen …"
                size="sm"
                ref="search_text_field"
                @keypress.enter.prevent="onSubmitFirstMemberResult"
            ></f-text>
            <div class="mt-2 grid grid-cols-[repeat(auto-fill,minmax(180px,1fr))] gap-2 col-span-2">
                <f-switch
                    :id="`members-${member.id}`"
                    :key="member.id"
                    :label="`${member.firstname} ${member.lastname}`"
                    v-for="member in search.results"
                    name="members[]"
                    :value="member.id"
                    v-model="values.members"
                    size="sm"
                    @keypress.enter.prevent="onSubmitMemberResult(member)"
                    inline
                ></f-switch>
            </div>
        </div>

        <button
            v-for="(compiler, index) in compilers"
            target="_BLANK"
            type="submit"
            name="type"
            :value="compiler.class"
            class="btn btn-primary mt-3 inline-block"
            v-text="compiler.title"
        ></button>
    </form>
</template>

<script>
import debounce from 'lodash/debounce';

export default {
    data: function () {
        return {
            search: {
                s: '',
                results: [],
            },
            values: {
                members: [],
                event_name: '',
                dateFrom: '',
                dateUntil: '',
                zipLocation: '',
                country: null,
                ...this.data,
            },
        };
    },
    props: {
        data: {},
        countries: {},
        compilers: {},
    },
    computed: {
        searchText: {
            get() {
                return this.search.s;
            },
            set: debounce(async function(event) {
                this.search.s = event;

                var response = await this.axios.post('/api/member/search', {search: event, minLength: 3});

                this.search.results = response.data.data;
            }, 300),
        },
    },
    methods: {
        onSubmitMemberResult(selected) {
            if (this.values.members.find((m) => m === selected.id) !== undefined) {
                this.values.members = this.values.members.filter((m) => m === selected.id);
            } else {
                this.values.members.push(selected.id);
            }

            this.searchText = '';
            this.$refs.search_text_field.$el.querySelector('input').focus();
        },
        onSubmitFirstMemberResult() {
            if (this.search.results.length === 0) {
                this.searchText = '';
                return;
            }

            this.onSubmitMemberResult(this.search.results[0]);
        },
    },

};
</script>
