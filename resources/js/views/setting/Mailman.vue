<template>
    <page-layout>
        <setting-layout>
            <form id="mailmansettingform" class="grow p-6 grid grid-cols-2 gap-3 items-start content-start" @submit.prevent="submit">
                <f-save-button form="mailmansettingform"></f-save-button>
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
                    <svg-sprite :src="stateDisplay.icon" :class="stateDisplay.text" class="w-5 h-5"></svg-sprite>
                    <span class="ml-3" :class="stateDisplay.text" v-text="stateDisplay.label"></span>
                </div>
                <f-text label="URL" hint="URL der Mailman Api" name="base_url" id="base_url" v-model="inner.base_url"></f-text>
                <f-text label="Benutzername" name="username" id="username" v-model="inner.username"></f-text>
                <f-text type="password" label="Passwort" name="password" id="password" v-model="inner.password"></f-text>
                <f-select label="Liste für alle Mitglieder" name="all_list" id="all_list" v-model="inner.all_list" :options="lists"></f-select>
                <f-select label="Liste für Eltern" name="all_parents_list" id="all_parents_list" v-model="inner.all_parents_list" :options="lists"></f-select>
                <f-select label="Liste für aktive Leiter" name="active_leaders_list" id="active_leaders_list" v-model="inner.active_leaders_list" :options="lists"></f-select>
                <f-select label="Liste für passive Leiter" name="passive_leaders_list" id="passive_leaders_list" v-model="inner.passive_leaders_list" :options="lists"></f-select>
                <div></div>
            </form>
        </setting-layout>
    </page-layout>
</template>

<script>
import SettingLayout from './Layout.vue';

export default {
    data: function () {
        return {
            inner: {...this.data},
        };
    },
    props: {
        data: {},
        state: {},
        lists: {},
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
            var _self = this;

            this.$inertia.post('/setting/mailman', this.inner, {
                onSuccess(page) {
                    _self.inner = page.props.data;
                },
            });
        },
    },
    components: {
        SettingLayout,
    },
};
</script>
