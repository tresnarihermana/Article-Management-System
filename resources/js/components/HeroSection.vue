<script setup lang="ts">
import { ref, watch, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    categories: Array,
});

const isSearchModalOpen = ref(false);
const searchInput = ref<HTMLInputElement | null>(null);
const searchModal = ref<HTMLElement | null>(null);

const query = ref('');
const results = ref<any[]>([]);
const loading = ref(false);
const activeResultIndex = ref(-1);
let timeout: number | null = null;

// --- Modal Logic ---

const openSearchModal = () => {
    if (query.value || document.activeElement === searchInput.value) {
        isSearchModalOpen.value = true;
    }
}

const closeSearchModal = () => {
    // We use a small delay to check if the focus has moved to the modal itself
    setTimeout(() => {
        // Check if the currently focused element is inside the input or the modal
        const isFocusWithin = searchInput.value === document.activeElement || searchModal.value?.contains(document.activeElement);
        if (!isFocusWithin) {
            isSearchModalOpen.value = false;
            activeResultIndex.value = -1; // Reset active index when closed
        }
    }, 100);
}

// Handler for mousedown on result item to prevent blur from firing and closing the modal
const handleResultClick = (id: number, slug: string) => {
    // Navigate to the article
    window.location.href = `/article/${id}/${slug}`;
    isSearchModalOpen.value = false;
    query.value = ''; // Clear query after selection
}

// --- Search Logic ---

watch(query, (val) => {
    if (timeout) clearTimeout(timeout);

    // Open modal immediately when typing starts
    openSearchModal();

    if (!val) {
        results.value = [];
        loading.value = false;
        return;
    }

    timeout = window.setTimeout(async () => {
        loading.value = true;
        try {
            const res = await fetch(`/api/articles?q=${encodeURIComponent(val)}`, {
                headers: { "X-Requested-With": "XMLHttpRequest" }
            });
            const data = await res.json();
            results.value = data.articles?.data ?? [];
        } catch (err) {
            console.error(err);
            results.value = [];
        } finally {
            loading.value = false;
            activeResultIndex.value = -1; // Reset after new results are loaded
        }
    }, 300); // debounce 300ms
});

// --- Keyboard Navigation ---

const handleKeyDown = (event: KeyboardEvent) => {
    if (!isSearchModalOpen.value || results.value.length === 0) return;

    const listLength = results.value.length;

    switch (event.key) {
        case 'Escape':
            isSearchModalOpen.value = false;
            searchInput.value?.blur();
            break;
        case 'ArrowDown':
            event.preventDefault();
            activeResultIndex.value = (activeResultIndex.value + 1) % listLength;
            break;
        case 'ArrowUp':
            event.preventDefault();
            activeResultIndex.value = (activeResultIndex.value - 1 + listLength) % listLength;
            break;
        case 'Enter':
            if (activeResultIndex.value > -1 && results.value[activeResultIndex.value]) {
                event.preventDefault();
                // Navigate to the selected result
                handleResultClick(results.value[activeResultIndex.value].id, results.value[activeResultIndex.value].slug);
            }
            break;
    }
}

// Add global keydown listener
onMounted(() => {
    document.addEventListener('keydown', handleKeyDown);
});

onUnmounted(() => {
    document.removeEventListener('keydown', handleKeyDown);
    if (timeout) clearTimeout(timeout);
});

const handleSearchSubmit = (event: Event) => {
    event.preventDefault();
    if (query.value) {
        window.location.href = `/search?q=${encodeURIComponent(query.value)}`;
    }
}
</script>

<template>
    <section class="bg-white py-24 antialiased md:py-16 lg:py-8 HeroSection">
        <div class="mx-auto grid max-w-screen-xl px-4 pb-8 md:grid-cols-12 lg:gap-12 lg:pb-16 xl:gap-0">
            <div class="content-center justify-self-start md:col-span-7 md:text-start">
                <h1
                    class="mb-4 text-5xl font-extrabold leading-tight tracking-tighter dark:text-white md:max-w-2xl md:text-6xl xl:text-7xl">
                    Find Your Next <span class="text-green-600">Inspiration.</span>
                </h1>

                <p
                    class="mb-4 max-w-2xl text-xl text-gray-700 dark:text-gray-300 md:mb-10 lg:mb-12 lg:text-2xl tracking-normal">
                    Explore our vast library of articles, tutorials, and community discussions on a wide range of
                    topics.
                </p>

                <form class="flex max-w-lg relative" @submit.prevent="handleSearchSubmit">
                    <div class="relative w-full">
                        <input type="search" id="search-input" ref="searchInput" v-model="query" autocomplete="off"
                            class="block w-full rounded-l-xl border border-gray-300 bg-gray-50 p-4 pe-12 text-lg text-gray-900 shadow-md transition duration-200 ease-in-out hover:shadow-lg focus:border-green-500 focus:ring-green-500 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white dark:placeholder-gray-400"
                            placeholder="Search for articles..." required @focus="openSearchModal"
                            @blur="closeSearchModal" />

                        <div class="absolute inset-y-0 end-0 flex items-center pe-3 pointer-events-none">
                            <svg class="h-6 w-6 text-gray-400 dark:text-gray-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>

                        <div v-if="isSearchModalOpen" ref="searchModal"
                            class="search-modal-container rounded-xl shadow-2xl overflow-hidden border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 absolute z-20 top-full mt-2 w-full"
                            tabindex="-1" @focusin="openSearchModal" @focusout="closeSearchModal">
                            <div class="p-4">
                                <p v-if="query" class="text-gray-500 dark:text-gray-400 mb-2 font-semibold">Results for
                                    "{{ query }}":</p>
                                <p v-else class="text-gray-500 dark:text-gray-400 mb-2 font-semibold">Start typing to
                                    search:</p>

                                <ul class="space-y-2 max-h-60 overflow-auto">
                                    <li v-if="loading" class="p-2 text-gray-500 flex items-center">
                                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-green-600"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        Searching...
                                    </li>
                                    <li v-else-if="results.length === 0 && query" class="p-2 text-gray-400">No articles
                                        found for "{{ query }}".</li>
                                    <li v-for="(article, index) in results" :key="article.id"
                                        class="p-2 rounded-lg transition duration-150 ease-in-out" :class="{
                                            // Menyempurnakan tampilan hasil yang aktif
                                            'bg-green-50 dark:bg-green-800': index === activeResultIndex,
                                            'hover:bg-gray-100 dark:hover:bg-gray-700': index !== activeResultIndex
                                        }" @mousedown.prevent="handleResultClick(article.id, article.slug)"
                                        :tabindex="index === activeResultIndex ? 0 : -1">
                                        <a :href="route('article.show', { id: article.id, slug: article.slug })"
                                            :class="{ 'text-green-800 dark:text-white font-semibold': index === activeResultIndex, 'text-gray-900 dark:text-white font-medium': index !== activeResultIndex }"
                                            class="block w-full">
                                            {{ article.title }}
                                        </a>
                                    </li>
                                </ul>
                                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <p
                                        class="text-sm text-gray-500 dark:text-gray-400 flex justify-between items-center">
                                        <span>
                                            <b>Navigate:</b> Use <kbd
                                                class="px-1 py-0.5 bg-gray-200 dark:bg-gray-700 rounded text-xs font-mono border border-gray-300 dark:border-gray-600">↑</kbd>
                                            and <kbd
                                                class="px-1 py-0.5 bg-gray-200 dark:bg-gray-700 rounded text-xs font-mono border border-gray-300 dark:border-gray-600">↓</kbd>.
                                        </span>
                                        <span v-if="results.length > 0">
                                            <kbd
                                                class="px-1 py-0.5 bg-gray-200 dark:bg-gray-700 rounded text-xs font-mono border border-gray-300 dark:border-gray-600">Enter</kbd>
                                            to select.
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit"
                        class="rounded-r-xl bg-green-600 px-6 py-4 text-lg font-bold text-white shadow-xl transition duration-200 ease-in-out hover:bg-green-700 hover:shadow-2xl hover:scale-[1.01] focus:outline-none focus:ring-4 focus:ring-green-400 dark:bg-green-600 dark:hover:bg-green-500 dark:focus:ring-green-700">
                        Search
                    </button>
                </form>
            </div>

            <div class="hidden md:col-span-5 md:mt-0 md:flex">
                <img src="Searching.svg" alt="Illustration of a person reading or searching" />
            </div>
        </div>
        <slot />
    </section>
</template>

<style>
.dark .HeroSection {
    background-image: url(/public/bg.jpg);
    background-position: center;
    background-repeat: no-repeat;
    background-size: cover, auto;
}

.search-modal-container {
    position: absolute;
    z-index: 50;
    width: 100%;
    margin-top: 4px;
    outline: none;
    /* Remove default focus outline on the modal container */
}
</style>