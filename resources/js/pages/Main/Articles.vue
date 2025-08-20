<script setup lang="ts">
import PageLayout from '@/layouts/PageLayout.vue';
import ArticleList from '@/components/ArticleList.vue';
import { Head, usePage } from '@inertiajs/vue3';
import SearchBar from '@/components/SearchBar.vue';
import SlidableCategories from '@/components/SlidableCategories.vue';
import PopularSectionSidebar from '@/components/PopularSectionSidebar.vue';
import { Inertia } from '@inertiajs/inertia';
import { computed } from 'vue';
const page = usePage()
const { categorized, categories, popArticles, ArticlesPagination } = page.props

const loadPage = (page: number) => {
  Inertia.get(route('articles.list'), { page }, {
    preserveState: true,
    preserveScroll: true
  })
}

const visiblePages = computed(() => {
  const total = ArticlesPagination.last_page
  const current = ArticlesPagination.current_page
  let start = Math.max(current - 2, 1)
  let end = Math.min(start + 4, total)
  start = Math.max(end - 4, 1) // pastikan selalu 5 maksimal
  const pages = []
  for (let i = start; i <= end; i++) pages.push(i)
  return pages
})
</script>

<template>

  <Head title="Articles">
    <link rel="preconnect" href="https://rsms.me/" />
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
  </Head>>
  <PageLayout>
    <SlidableCategories :categories="categories" class="max-w-[1200px] top-15" />
    <ArticleList :categorized="categorized.data" :ArticlesPagination="ArticlesPagination">
<template #pagination>
  <nav aria-label="Page navigation" class=" mx-auto relative max-w-[1200px]">
    <ul class="flex justify-center items-center -space-x-px h-10 text-base">
      <li>
        <button @click="loadPage(ArticlesPagination.current_page - 1)"
          :disabled="ArticlesPagination.current_page === 1"
          class="flex items-center justify-center px-4 h-10 ms-0 leading-tight text-gray-500 bg-white border border-e-0 border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-400 dark:hover:bg-zinc-700 dark:hover:text-white">
          <span class="sr-only">Previous</span>
          <svg class="w-3 h-3 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
            viewBox="0 0 6 10">
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
          <svg class="w-3 h-3 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
            viewBox="0 0 6 10">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="m1 9 4-4-4-4" />
          </svg>
        </button>
      </li>
    </ul>
  </nav>
</template>

      <template #sidebar>
        <PopularSectionSidebar :popArticles="popArticles"></PopularSectionSidebar>
      </template>
    </ArticleList>
  </PageLayout>
</template>
