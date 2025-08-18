<script setup lang="ts">
import PageLayout from '@/layouts/PageLayout.vue';
import { Head } from '@inertiajs/vue3';
const props = defineProps<{
  articles: Array<any>,
    filters: Object,
}>();
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
              Search on <q>{{ filters.search }}</q>
            </h2>

            <div class="flex flex-wrap -mx-3" v-if="articles.length === 0">
              <div class="flex-shrink max-w-full w-full sm:w-1/3 px-3 pb-3">
                <h1 class="text-2xl">Articles not found</h1>
              </div>
            </div>

            <div class="flex flex-wrap -mx-3" v-else>
              <div v-for="article in articles" :key="article.id" class="flex-shrink max-w-full w-full sm:w-1/3 px-3 pb-3">
                <div class="flex flex-row sm:block hover-img">
                  <a :href="route('article.show', article.slug)">
                    <img class="max-w-full w-full mx-auto" :src="`/storage/${article.cover}`" :alt="article.title" />
                  </a>
                  <div class="py-0 sm:py-3 pl-3 sm:pl-0">
                    <h3 class="text-lg font-bold leading-tight mb-2">
                      <a :href="route('article.show', article.slug)">{{ article.title }}</a>
                    </h3>
                    <p class="hidden md:block text-gray-600 leading-tight mb-1 dark:text-gray-400"
                       v-html="article.excerpt.length < 120 ? article.excerpt : article.excerpt.slice(0,120)+'...'">
                    </p>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>


      </div>
    </div>
  </div>
</PageLayout>
</template>
