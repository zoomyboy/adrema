<template>
    <form action="/contribution/generate" target="_BLANK" class="max-w-2xl w-full mx-auto gap-6 grid-cols-2 grid p-6">
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

        <div class="border-gray-200 shadow shadow-primary-700 p-3 shadow-[0_0_4px_gray] col-span-2">
            <f-text
                class="col-span-2"
                id="membersearch"
                name="membersearch"
                v-model="membersearch"
                label="Suchen …"
                size="sm"
                ref="membersearchfield"
                @keypress.enter.prevent="onSubmitFirstMemberResult"
            ></f-text>
            <div class="mt-2 grid grid-cols-[repeat(auto-fill,minmax(160px,1fr))] gap-2 col-span-2">
                <f-switch
                    :id="`members-${member.id}`"
                    :key="member.id"
                    :label="`${member.firstname} ${member.lastname}`"
                    v-for="member in memberResults"
                    name="members[]"
                    :value="member.id"
                    v-model="values.members"
                    @keypress.enter.prevent="onSubmitMemberResult(member)"
                ></f-switch>
            </div>
        </div>

        <button
            target="_BLANK"
            type="submit"
            name="type"
            value="\App\Contribution\SolingenData"
            class="btn btn-primary mt-3 inline-block"
        >
            Für Stadt erstellen
        </button>
    </form>
</template>

<script>
export default {
    data: function () {
        return {
            membersearch: '',
            values: {
                members: [],
                event_name: '',
                dateFrom: '',
                dateUntil: '',
            },
        };
    },
    props: {
        allMembers: {},
    },
    computed: {
        memberResults() {
            if (this.membersearch.length === 0) {
                return this.allMembers;
            }

            return this.allMembers.filter(
                (member) =>
                    (member.firstname + ' ' + member.lastname)
                        .toLowerCase()
                        .indexOf(this.membersearch.toLowerCase()) !== -1
            );
        },
    },
    methods: {
        onSubmitMemberResult(selected) {
            if (this.values.members.find((m) => m === selected.id) !== undefined) {
                this.values.members = this.values.members.filter((m) => m === selected.id);
            } else {
                this.values.members.push(selected.id);
            }

            this.membersearch = '';
            this.$refs.membersearchfield.$el.querySelector('input').focus();
        },
        onSubmitFirstMemberResult() {
            if (this.memberResults.length === 0) {
                this.membersearch = '';
                return;
            }

            this.onSubmitMemberResult(this.memberResults[0]);
        },
    },
};
</script>
