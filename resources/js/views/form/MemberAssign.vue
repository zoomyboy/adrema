<template>
    <div>
        <div class="mt-4">
            <f-text id="search_string" v-model="searchString" label="Mitglied finden"></f-text>
        </div>
        <div v-if="results !== null" class="mt-5 sm:mt-10 space-y-2">
            <ui-search-result v-for="member in results.hits" :key="member.id" :member="member">
                <template #buttons>
                    <button class="btn btn-primary btn-sm" @click.prevent="emit('assign', member.id)">Zuweisen</button>
                </template>
            </ui-search-result>
        </div>
    </div>
</template>

<script lang="js" setup>
import useSearch from '../../composables/useSearch.js';
const emit = defineEmits(['assign']);

const { searchString, results } = useSearch(null, { limit: 10 });
</script>
