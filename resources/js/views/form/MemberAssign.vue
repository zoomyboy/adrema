<template>
    <div>
        <div class="mt-4">
            <f-text id="search_string" v-model="searchString" label="Mitglied finden"></f-text>
        </div>
        <div v-if="results !== null" class="mt-5 sm:mt-10 space-y-2">
            <a v-for="member in results.hits" :key="member.id" href="#" @click.prevent="emit('assign', member.id)">
                <div class="flex items-center justify-between hover:bg-sky-600/20 transition text-sky-300 px-3 sm:px-6 py-1 sm:py-3 rounded-lg">
                    <div class="flex space-x-2 items-center">
                        <div class="w-5 sm:w-16 flex flex-none">
                            <ui-age-groups icon-class="w-4 h-4 sm:w-6 sm:h-6" class="flex-col sm:flex-row" :member="member"></ui-age-groups>
                        </div>
                        <div class="flex items-baseline flex-col md:flex-row">
                            <span class="text-lg" v-text="member.fullname"></span>
                            <span class="ml-2 text-xs" v-text="member.group_name"></span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</template>

<script lang="js" setup>
import useSearch from '../../composables/useSearch.js';
const emit = defineEmits(['assign']);

const { searchString, results } = useSearch(null, { limit: 10 });
</script>
