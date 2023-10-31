<template>
    <page-layout>
        <form target="_BLANK" class="max-w-4xl w-full mx-auto gap-6 grid-cols-2 grid p-6">
            <f-text id="eventName" v-model="values.eventName" name="eventName" class="col-span-2"
                label="Veranstaltungs-Name" required></f-text>
            <f-text id="dateFrom" v-model="values.dateFrom" name="dateFrom" type="date" label="Datum von" required></f-text>
            <f-text id="dateUntil" v-model="values.dateUntil" name="dateUntil" type="date" label="Datum bis"
                required></f-text>

            <f-text id="zipLocation" v-model="values.zipLocation" name="zipLocation" label="PLZ / Ort" required></f-text>
            <f-select id="country" v-model="values.country" :options="countries" name="country" label="Land"
                required></f-select>

            <div class="border-gray-200 shadow shadow-primary-700 p-3 shadow-[0_0_4px_gray] col-span-2">
                <f-text id="search_text" ref="search_text_field" v-model="searchText" class="col-span-2" name="search_text"
                    label="Suchen …" size="sm" @keypress.enter.prevent="onSubmitFirstMemberResult"></f-text>
                <div class="mt-2 grid grid-cols-[repeat(auto-fill,minmax(180px,1fr))] gap-2 col-span-2">
                    <f-switch v-for="member in search.results" :id="`members-${member.id}`" :key="member.id"
                        v-model="values.members" :label="`${member.firstname} ${member.lastname}`" name="members[]"
                        :value="member.id" size="sm" inline
                        @keypress.enter.prevent="onSubmitMemberResult(member)"></f-switch>
                </div>
            </div>

            <button v-for="(compiler, index) in compilers" class="btn btn-primary mt-3 inline-block" @click.prevent="
                values.type = compiler.class;
            submit();
                                                " v-text="compiler.title"></button>
        </form>
    </page-layout>
</template>

<script>
import debounce from 'lodash/debounce';

export default {
    props: {
        data: {},
        countries: {},
        compilers: {},
    },
    data: function () {
        return {
            search: {
                s: '',
                results: [],
            },
            values: {
                type: null,
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
    computed: {
        searchText: {
            get() {
                return this.search.s;
            },
            set: function (event) {
                this.search.s = event;

                debounce(async () => {
                    var response = await this.axios.post(
                        '/api/member/search',
                        {
                            filter: {
                                search: event,
                                hasBirthday: true,
                                hasFullAddress: true,
                            },
                        },
                        { headers: { 'X-Meta': 'false' } }
                    );

                    this.search.results = response.data.data;
                }, 300)();
            },
        },
    },
    methods: {
        async submit() {
            try {
                await this.axios.post('/contribution-validate', this.values);
                var payload = btoa(encodeURIComponent(JSON.stringify(this.values)));
                window.open(`/contribution-generate?payload=${payload}`);
            } catch (e) {
                this.errorsFromException(e);
            }
        },
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
