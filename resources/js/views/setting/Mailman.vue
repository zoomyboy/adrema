<template>
    <page-layout>
        <template #right>
            <f-save-button form="mailmansettingform"></f-save-button>
        </template>
        <setting-layout>
            <form id="mailmansettingform" class="grow p-6 grid grid-cols-2 gap-3 items-start content-start" @submit.prevent="submit">
                <div class="col-span-full text-gray-100 mb-3">
                    <p class="text-sm">
                        Scoutrobot kann automatisch Mailinglisten erstellen, wenn es mit einem existierenden
                        <a href="https://docs.mailman3.org/en/latest/">Mailman Server</a> verbunden wird. Mailman ist ein OpenSource-Mailinglisten-System, um E-Mails an mehrere Leute zu senden.
                    </p>
                    <p class="text-sm mt-1">Scoutrobot wird nach der Ersteinrichtung deine Mitglieder zu bestehenden E-Mail-Verteilern hinzufügen.</p>
                </div>
                <div>
                    <f-switch id="is_active" v-model="inner.is_active" label="Mailman-Synchronisation aktiv"></f-switch>
                </div>
                <div class="flex h-full items-center">
                    <ui-sprite :src="stateDisplay.icon" :class="stateDisplay.text" class="w-5 h-5"></ui-sprite>
                    <span class="ml-3" :class="stateDisplay.text" v-text="stateDisplay.label"></span>
                </div>
                <f-text id="base_url" v-model="inner.base_url" label="URL" hint="URL der Mailman Api"></f-text>
                <f-text id="username" v-model="inner.username" label="Benutzername"></f-text>
                <f-text id="password" v-model="inner.password" type="password" label="Passwort"></f-text>
                <f-select id="all_list" v-model="inner.all_list" label="Liste für alle Mitglieder" name="all_list" :options="lists"></f-select>
                <f-select id="all_parents_list" v-model="inner.all_parents_list" label="Liste für Eltern" name="all_parents_list" :options="lists"></f-select>
                <f-select id="active_leaders_list" v-model="inner.active_leaders_list" label="Liste für aktive Leiter" name="active_leaders_list" :options="lists"></f-select>
                <f-select id="passive_leaders_list" v-model="inner.passive_leaders_list" label="Liste für passive Leiter" name="passive_leaders_list" :options="lists"></f-select>
                <div></div>
            </form>
        </setting-layout>
    </page-layout>
</template>

<script>
import SettingLayout from './Layout.vue';

export default {
    components: {
        SettingLayout,
    },
    props: {
        data: {},
        state: {},
        lists: {},
    },
    data: function () {
        return {
            inner: {...this.data},
        };
    },
    computed: {
        stateDisplay() {
            if (this.state === null) {
                return {
                    text: 'text-gray-500',
                    icon: 'disabled',
                    label: 'Deaktiviert',
                };
            }

            return this.state
                ? {
                      text: 'text-green-500',
                      icon: 'check',
                      label: 'Verbindung erfolgreich.',
                  }
                : {
                      text: 'text-red-500',
                      icon: 'close',
                      label: 'Verbindung fehlgeschlagen.',
                  };
        },
    },

    methods: {
        submit() {
            this.$inertia.post('/setting/mailman', this.inner, {
                onSuccess: (page) => {
                    this.$success('Einstellungen gespeichert.');
                    this.inner = page.props.data;
                },
            });
        },
    },
};
</script>
