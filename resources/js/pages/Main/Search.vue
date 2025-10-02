<script setup lang="ts">
import PageLayout from '@/layouts/PageLayout.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, onMounted, ref } from 'vue';

const props = defineProps<{
    articles: Array<any>;
    filters: Object;
    ArticlesPagination: Object;
    data_mode: string;
}>();

const articles = ref(props.data_mode === 'api' ? [] : props.articles.data);
const ArticlesPagination = ref(props.data_mode === 'api' ? {} : props.ArticlesPagination);
const articlesTotal = ref(props.data_mode === 'api' ? 0 : props.ArticlesPagination?.total || 0);

const isLoading = ref(props.data_mode === 'api');

const loadPage = async (page: number) => {
    if (props.data_mode !== 'api') return;
    if (ArticlesPagination.value.last_page && (page < 1 || page > ArticlesPagination.value.last_page)) return;

    isLoading.value = true; // mulai loading

    try {
        const res = await axios.get(`http://localhost:8001/api/articles/tp`, {
            params: { q: props.filters.search, page: page },
        });

        articles.value = res.data.articles.data.map((article) => ({
            id: article.id,
            title: article.title,
            channel: article.channel,
            category_id: article.category_id,
            sub_title: article.sub_title,
            slug: article.url_alias,
            excerpt: article.summary,
            image: `https://srv1.portal.p-cd.net/850p/${article.image}`,
        }));

        ArticlesPagination.value = res.data.ArticlesPagination;
        articlesTotal.value = res.data.articlesTotal;
    } finally {
        isLoading.value = false; // selesai loading
    }
};

onMounted(() => {
    // Only call loadPage on mount if we are in 'api' mode
    if (props.data_mode === 'api') {
        loadPage(1);
    }
});

const visiblePages = computed(() => {
    // Now use the reactive ArticlesPagination.value
    const total = ArticlesPagination.value.last_page || 1;
    const current = ArticlesPagination.value.current_page || 1;
    let start = Math.max(current - 2, 1);
    let end = Math.min(start + 4, total);
    start = Math.max(end - 4, 1);
    const pages = [];
    for (let i = start; i <= end; i++) pages.push(i);
    return pages;
});

const onImgError = (event: Event) => {
    (event.target as HTMLImageElement).src = 'storage/placeholder/placeholder.png';
};

// Expose the variables and functions needed by the template
// Since we used <script setup>, they are auto-exposed, but this comment serves as a reminder.
</script>

<template>
    <Head title="Search">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <PageLayout>
        <div class="py-6">
            <div class="mx-auto px-3 sm:px-4 xl:container xl:px-2">
                <div class="flex flex-wrap">
                    <div class="w-full overflow-hidden py-10 lg:w-2/3">
                        <div class="w-full py-3">
                            <h2 class="mb-4 text-2xl font-bold text-gray-800 dark:text-gray-300">
                                <span class="mr-2 inline-block h-5 border-l-4 border-green-600"></span>
                                Found {{ ArticlesPagination.total }} results of total {{ articlesTotal }} for "{{ filters.search }}"
                            </h2>

                            <div class="-mx-3 flex flex-wrap">
                                <template v-if="isLoading">
                                    <!-- Skeleton placeholder 6 items -->
                                    <div v-for="n in 6" :key="n" class="w-full max-w-full flex-shrink px-3 pb-3 sm:w-1/3">
                                        <div class="animate-pulse">
                                            <div class="h-48 w-full rounded bg-gray-300 dark:bg-zinc-700"></div>
                                            <div class="mt-2 h-4 w-3/4 rounded bg-gray-300 dark:bg-zinc-600"></div>
                                            <div class="mt-1 h-3 w-1/2 rounded bg-gray-300 dark:bg-zinc-600"></div>
                                        </div>
                                    </div>
                                </template>

                                <template v-else-if="articles.length === 0">
                                    <div class="w-full max-w-full flex-shrink px-3 pb-3 sm:w-1/3">
                                        <h1 class="text-2xl">Articles not found</h1>
                                    </div>
                                </template>

                                <template v-else>
                                    <div v-for="article in articles" :key="article.id" class="w-full max-w-full flex-shrink px-3 pb-3 sm:w-1/3">
                                        <!-- Card article seperti sebelumnya -->
                                        <div class="hover-img flex flex-row sm:block">
                                            <a :href="route('article.show', { id: article.id, slug: article.slug })">
                                                <img
                                                    class="mx-auto aspect-video w-full max-w-full"
                                                    :src="article.image ? article.image : article.cover_url"
                                                    :alt="article.title"
                                                    @error="onImgError"
                                                />
                                            </a>
                                            <div class="py-0 pl-3 sm:py-3 sm:pl-0">
                                                <h3 class="mb-2 text-lg leading-tight font-bold">
                                                    <a :href="route('article.show', { id: article.id, slug: article.slug })">{{ article.title }}</a>
                                                </h3>
                                                <p
                                                    class="mb-1 hidden leading-tight text-gray-600 md:block dark:text-gray-400"
                                                    v-html="article.excerpt.length < 120 ? article.excerpt : article.excerpt.slice(0, 120) + '...'"
                                                ></p>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <nav aria-label="Page navigation" class="relative mx-auto max-w-[1200px]" v-if="ArticlesPagination.last_page > 1">
                                <ul class="flex h-10 items-center justify-center -space-x-px text-base">
                                    <li>
                                        <button
                                            @click="loadPage(ArticlesPagination.current_page - 1)"
                                            :disabled="ArticlesPagination.current_page === 1"
                                            class="ms-0 flex h-10 items-center justify-center rounded-s-lg border border-e-0 border-gray-300 bg-white px-4 leading-tight text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-400 dark:hover:bg-zinc-700 dark:hover:text-white"
                                        >
                                            <span class="sr-only">Previous</span>
                                            <svg
                                                class="h-3 w-3 rtl:rotate-180"
                                                aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg"
                                                fill="none"
                                                viewBox="0 0 6 10"
                                            >
                                                <path
                                                    stroke="currentColor"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M5 1 1 5l4 4"
                                                />
                                            </svg>
                                        </button>
                                    </li>

                                    <li v-for="page in visiblePages" :key="page">
                                        <button
                                            @click="loadPage(page)"
                                            :class="
                                                page === ArticlesPagination.current_page
                                                    ? 'z-10 flex h-10 items-center justify-center border border-blue-300 bg-blue-50 px-4 leading-tight text-blue-600 dark:border-zinc-700 dark:bg-zinc-700 dark:text-white'
                                                    : 'flex h-10 items-center justify-center border border-zinc-300 bg-white px-4 leading-tight text-zinc-500 hover:bg-zinc-100 hover:text-zinc-700 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-400 dark:hover:bg-zinc-700 dark:hover:text-white'
                                            "
                                        >
                                            {{ page }}
                                        </button>
                                    </li>

                                    <li>
                                        <button
                                            @click="loadPage(ArticlesPagination.current_page + 1)"
                                            :disabled="ArticlesPagination.current_page === ArticlesPagination.last_page"
                                            class="flex h-10 items-center justify-center rounded-e-lg border border-zinc-300 bg-white px-4 leading-tight text-zinc-500 hover:bg-zinc-100 hover:text-zinc-700 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-400 dark:hover:bg-zinc-700 dark:hover:text-white"
                                        >
                                            <span class="sr-only">Next</span>
                                            <svg
                                                class="h-3 w-3 rtl:rotate-180"
                                                aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg"
                                                fill="none"
                                                viewBox="0 0 6 10"
                                            >
                                                <path
                                                    stroke="currentColor"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="m1 9 4-4-4-4"
                                                />
                                            </svg>
                                        </button>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </PageLayout>
</template>
