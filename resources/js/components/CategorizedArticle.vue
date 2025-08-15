<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

const props = defineProps<{
  category: Array<any>,
}>();
</script>

<template>

  <Head :title="category[0]?.name">
    <link rel="preconnect" href="https://rsms.me/" />
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
  </Head>
  <div class="py-6">
    <div class="xl:container mx-auto px-3 sm:px-4 xl:px-2">
      <div class="flex flex-wrap">
        <div class="w-full lg:w-2/3 overflow-hidden py-10">
          <div v-if="category.length" class="w-full py-3">
            <h2 class="text-gray-800 text-2xl font-bold mb-4 dark:text-gray-300">
              <span class="inline-block h-5 border-l-4 border-green-600 mr-2"></span>
              {{ category[0].name }}
            </h2>

            <div class="flex flex-wrap -mx-3" v-if="category[0].articles.length === 0">
              <div class="flex-shrink max-w-full w-full sm:w-1/3 px-3 pb-3">
                <h1 class="text-2xl">Article belum tersedia</h1>
              </div>
            </div>
            <div class="flex flex-wrap -mx-3">
              <div v-for="article in category[0].articles" :key="article.id"
                class="flex-shrink max-w-full w-full sm:w-1/3 px-3 pb-3">
                <div class="flex flex-row sm:block hover-img">
                  <a :href="route('article.show', article.slug)">
                    <img class="max-w-full w-full mx-auto" :src="`/storage/${article.cover}`" :alt="article.title" />
                  </a>
                  <div class="py-0 sm:py-3 pl-3 sm:pl-0">
                    <h3 class="text-lg font-bold leading-tight mb-2">
                      <a :href="route('article.show', article.slug)">{{ article.title }}</a>
                    </h3>
                    <p class="hidden md:block text-gray-600 leading-tight mb-1 dark:text-gray-400"
                      v-html="article.excerpt.length < 120 ? article.excerpt : article.excerpt.slice(0, 120) + '...'"></p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="w-full lg:w-1/3 lg:pl-8 lg:pt-14 lg:pb-8 order-first lg:order-last pt-10">
          <div class="w-full bg-white dark:bg-zinc-800">
            <div class="mb-6">
              <div class="p-4 bg-gray-100 dark:bg-zinc-900">
                <h2 class="text-lg font-bold dark:text-green-500">Most Popular in <span
                    class="underline decoration-2 decoration-green-300 dark:decoration-white dark:decoration-1">{{
                    category[0].name }}</span></h2>
              </div>
              <ul class="post-number">
                <li v-for="pop in category.flatMap(cat => cat.articles).slice(0, 5)" :key="pop.id"
                  class="border-b border-gray-100 dark:border-gray-800 dark:hover:bg-gray-700 hover:bg-gray-50">
                  <a class="text-lg font-bold px-6 py-3 flex flex-row items-center" :href="'/article/' + pop.slug">
                    {{ pop.title.length > 30 ? pop.title.slice(0, 30) + '...' : pop.title }}
                  </a>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
