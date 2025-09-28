<script setup lang="ts">
import PageLayout from '@/layouts/PageLayout.vue';
import { Inertia } from '@inertiajs/inertia';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';
const props = defineProps<{
  articles: Array<any>,
  filters: Object,
  ArticlesPagination: Object,
}>();
const loadPage = (page: number) => {
Inertia.get(route('search.list'), {
  search: props.filters.search,
  page: page
}, {
  preserveState: true,
  preserveScroll: true
})

}

const visiblePages = computed(() => {
  const total = props.ArticlesPagination.last_page
  const current = props.ArticlesPagination.current_page
  let start = Math.max(current - 2, 1)
  let end = Math.min(start + 4, total)
  start = Math.max(end - 4, 1) // pastikan selalu 5 maksimal
  const pages = []
  for (let i = start; i <= end; i++) pages.push(i)
  return pages
})
</script>

<template>

  <Head title="Search">
    <link rel="preconnect" href="https://rsms.me/" />
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
  </Head>
  <PageLayout>
    <div class="py-6">
      <div class="xl:container mx-auto px-3 sm:px-4 xl:px-2">
        <div class="flex flex-wrap">
          <div class="w-full lg:w-2/3 overflow-hidden py-10">
            <div class="w-full py-3">
              <h2 class="text-gray-800 text-2xl font-bold mb-4 dark:text-gray-300">
                <span class="inline-block h-5 border-l-4 border-green-600 mr-2"></span>
                {{ ArticlesPagination.total }} result of  <q>{{ filters.search }}</q> 
              </h2>

              <div class="flex flex-wrap -mx-3" v-if="articles.length === 0">
                <div class="flex-shrink max-w-full w-full sm:w-1/3 px-3 pb-3">
                  <h1 class="text-2xl">Articles not found</h1>
                </div>
              </div>

              <div class="flex flex-wrap -mx-3" v-else>
                <div v-for="article in articles.data" :key="article.id"
                  class="flex-shrink max-w-full w-full sm:w-1/3 px-3 pb-3">
                  <div class="flex flex-row sm:block hover-img">
                    <a :href="route('article.show', { id: article.id, slug: article.slug })
">
                      <img class="max-w-full w-full mx-auto" :src="`/storage/${article.cover}`" :alt="article.title" />
                    </a>
                    <div class="py-0 sm:py-3 pl-3 sm:pl-0">
                      <h3 class="text-lg font-bold leading-tight mb-2">
                        <a :href="route('article.show', { id: article.id, slug: article.slug })
">{{ article.title }}</a>
                      </h3>
                      <p class="hidden md:block text-gray-600 leading-tight mb-1 dark:text-gray-400"
                        v-html="article.excerpt.length < 120 ? article.excerpt : article.excerpt.slice(0, 120) + '...'">
                      </p>
                    </div>
                  </div>
                </div>
              </div>
              <nav aria-label="Page navigation" class=" mx-auto relative max-w-[1200px]" v-if="ArticlesPagination.last_page > 1">
                <ul class="flex justify-center items-center -space-x-px h-10 text-base">
                  <li>
                    <button @click="loadPage(ArticlesPagination.current_page - 1)"
                      :disabled="ArticlesPagination.current_page === 1"
                      class="flex items-center justify-center px-4 h-10 ms-0 leading-tight text-gray-500 bg-white border border-e-0 border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-400 dark:hover:bg-zinc-700 dark:hover:text-white">
                      <span class="sr-only">Previous</span>
                      <svg class="w-3 h-3 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M5 1 1 5l4 4" />
                      </svg>
                    </button>
                  </li>

                  <li v-for="page in visiblePages" :key="page">
                    <button @click="loadPage(page)"
                      :class="page === ArticlesPagination.current_page
                        ? 'z-10 flex items-center justify-center px-4 h-10 leading-tight text-blue-600 border border-blue-300 bg-blue-50 dark:border-zinc-700 dark:bg-zinc-700 dark:text-white'
                        : 'flex items-center justify-center px-4 h-10 leading-tight text-zinc-500 bg-white border border-zinc-300 hover:bg-zinc-100 hover:text-zinc-700 dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-400 dark:hover:bg-zinc-700 dark:hover:text-white'">
                      {{ page }}
                    </button>
                  </li>

                  <li>
                    <button @click="loadPage(ArticlesPagination.current_page + 1)"
                      :disabled="ArticlesPagination.current_page === ArticlesPagination.last_page"
                      class="flex items-center justify-center px-4 h-10 leading-tight text-zinc-500 bg-white border border-zinc-300 rounded-e-lg hover:bg-zinc-100 hover:text-zinc-700 dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-400 dark:hover:bg-zinc-700 dark:hover:text-white">
                      <span class="sr-only">Next</span>
                      <svg class="w-3 h-3 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="m1 9 4-4-4-4" />
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
