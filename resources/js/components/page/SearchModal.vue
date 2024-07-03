<template>
    <div class="fixed z-40 top-0 left-0 w-full h-full flex items-start pt-12 justify-center p-6 bg-black/60" @click.self="emit('close')">
        <div class="relative rounded-lg p-4 sm:p-8 shadow-2xl shadow-black border border-sky-700/30 bg-sky-800/10 border-solid w-full max-w-[50rem] overflow-auto backdrop-blur-lg">
            <div class="relative">
                <input
                    ref="searchInput"
                    v-model="searchString"
                    type="text"
                    class="w-full px-3 sm:px-5 py-2 sm:py-3 pl-12 sm:pl-16 rounded-xl sm:rounded-2xl border-sky-200/40 text-sky-200 sm:text-xl placeholder-sky-200/40 bg-sky-600/20"
                    placeholder="Wer suchet, der findet …"
                />
                <div class="absolute flex items-center h-full top-0 left-4">
                    <ui-sprite src="search" class="w-5 h-5 sm:w-7 sm:h-7 text-sky-200/20" />
                </div>
            </div>
            <div v-if="results.hits.length" class="mt-5 sm:mt-10 space-y-2">
                <ui-search-result v-for="member in results.hits" :key="member.id" :member="member">
                    <template #buttons>
                        <i-link v-tooltip="`Details`" :href="member.links.show" class="inline-flex btn btn-primary btn-sm" @click="emit('close')"><ui-sprite src="eye"></ui-sprite></i-link>
                        <i-link v-tooltip="`Bearbeiten`" :href="member.links.edit" class="inline-flex btn btn-warning btn-sm" @click="emit('close')"><ui-sprite src="pencil"></ui-sprite></i-link>
                    </template>
                </ui-search-result>
            </div>
        </div>
    </div>
</template>

<script lang="js" setup>
import { ref, onMounted } from 'vue';
import useSearch from '../../composables/useSearch.js';
const emit = defineEmits(['close']);

const { searchString, results } = useSearch(null, { limit: 10 });

const searchInput = ref(null);

onMounted(() => {
    searchInput.value.focus();
});
</script>
