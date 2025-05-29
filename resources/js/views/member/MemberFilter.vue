<template>
    <f-switch
        v-show="hasModule('bill')"
        id="ausstand"
        name="ausstand"
        :model-value="getFilter('ausstand')"
        label="Nur Ausstände"
        size="sm"
        @update:model-value="setFilter('ausstand', $event)"
    ></f-switch>
    <f-select
        id="has_vk"
        name="has_vk"
        :model-value="getFilter('has_vk')"
        label="Verhaltenskodex unterschrieben"
        size="sm"
        :options="meta.boolean_filter"
        @update:model-value="setFilter('has_vk', $event)"
    ></f-select>
    <f-select
        id="has_svk"
        name="has_svk"
        :model-value="getFilter('has_svk')"
        label="SVK unterschrieben"
        size="sm"
        :options="meta.boolean_filter"
        @update:model-value="setFilter('has_svk', $event)"
    ></f-select>
    <f-multipleselect
        id="group_ids"
        :options="meta.groups"
        :model-value="getFilter('group_ids')"
        label="Gruppierungen"
        size="sm"
        @update:model-value="setFilter('group_ids', $event)"
    ></f-multipleselect>
    <f-select
        v-show="hasModule('bill')"
        id="billKinds"
        name="billKinds"
        :options="meta.billKinds"
        :model-value="getFilter('bill_kind')"
        label="Rechnung"
        size="sm"
        @update:model-value="setFilter('bill_kind', $event)"
    ></f-select>

    <div>
        <div>nach mitgitedschaftren</div>
        <button class="btn btn-primary label mt-2" @click.prevent="memberships = [...memberships, {...meta.default_membership_filter}]">
            <ui-sprite class="w-3 h-3 xl:mr-2" src="plus"></ui-sprite>
            <span class="hidden xl:inline">Hinzufügen</span>
        </button>
        <div v-for="(filter, index) in memberships" :key="index" class="flex space-x-2 mt-2">
            <f-multipleselect :id="`group_ids-multiple-${index}`" v-model="filter.group_ids" :options="meta.groups" label="Gruppierung" size="sm"></f-multipleselect>
            <f-multipleselect :id="`activity_ids-multiple-${index}`" v-model="filter.activity_ids" :options="meta.filterActivities" label="Tätigkeiten" size="sm"></f-multipleselect>
            <f-multipleselect :id="`subactivity_ids-multiple-${index}`" v-model="filter.subactivity_ids" :options="meta.filterSubactivities" label="Untertätigkeiten" size="sm"></f-multipleselect>
        </div>
        <button
            class="btn btn-primary label mt-3"
            @click.prevent="
                setFilterObject({...filter, memberships: membershipFilters});
                membershipFilters = null;
            "
        >
            <span class="hidden xl:inline">Anwenden</span>
        </button>
    </div>
</template>

<script setup>
import {onMounted, ref, watch} from 'vue';

const emit = defineEmits(['update:modelValue']);

const props = defineProps({
    modelValue: {
        type: Object,
        required: true,
    },
    meta: {
        type: Object,
        required: true,
    },
});

function getFilter(key) {
    return props.modelValue[key];
}

function setFilter(key, value) {
    emit('update:modelValue', {
        ...props.modelValue,
        [key]: value,
    });
}

const memberships = ref([]);

onMounted(() => {
    memberships.value = props.modelValue.memberships;
});

watch(
    memberships,
    function (newValue) {
        console.log('II');
        setFilter('meberships', newValue);
    },
    {deep: true}
);
</script>
