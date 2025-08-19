<script setup>
import PageLayout from '@/layouts/PageLayout.vue';
import { useInitials } from '../../composables/useInitials';

defineProps({
    author: Object,
    articles: Object, // <- dari controller
})

const { getInitials } = useInitials()
</script>

<template>
<PageLayout>
  <div class="job-card bg-white dark:bg-zinc-800 mt-20 shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300 relative mx-auto max-w-[1200px]">
    <div class="relative h-64">
      <img 
        v-if="author.cover" 
        class="w-full h-full object-cover"
        :src="`/storage/${author.cover}`" 
        alt="Author cover"
      >
      <div v-else class="w-full h-full bg-gradient-to-r from-orange-500 to-red-600"></div>
      <img 
        class="absolute -bottom-8 left-4 w-42 h-42 rounded-full border-4 border-white dark:border-gray-800 object-cover" 
        :src="author.avatar ? `/storage/${author.avatar}` : `https://ui-avatars.com/api/?name=${getInitials(author.username)}&background=random`"
        alt="Author avatar">
    </div>

    <div class="pt-12 px-4 pb-4">
      <div class="flex justify-between items-start">
        <div>
          <h3 class="font-bold text-lg dark:text-white">{{ author.username }} ({{  author.name }})</h3>
          <p class="text-sm font-medium text-gray-600 dark:text-gray-300 mt-1">{{ author.bio ?? ''}}</p>
        </div>
        <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full dark:bg-blue-900 dark:text-blue-200">
          {{ author.roles[0].name ?? 'user'}}
        </span>
      </div>

      <div class="mt-4 flex items-center text-sm text-gray-500 dark:text-gray-400">
        <svg class="w-5 h-5 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        <span>San Francisco, CA · Remote</span>
      </div>

      <div class="mt-4 flex flex-wrap gap-2">
        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full dark:bg-blue-900 dark:text-blue-200">Python</span>
        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full dark:bg-blue-900 dark:text-blue-200">Machine Learning</span>
        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full dark:bg-blue-900 dark:text-blue-200">SQL</span>
      </div>

      <div class="mt-4 flex items-center">
        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
          <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <span>Author sejak {{ new Date(author.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) }}</span>
        </div>
        <span class="text-sm ml-5 text-gray-500 dark:text-gray-400"><li>{{ articles.total }} articles</li></span>
      </div>
    </div>
  </div>

  <ul class="grid grid-cols-1 xl:grid-cols-3 gap-y-10 gap-x-6 items-stretch p-8 mx-auto max-w-[1200px] relative">
    <li v-for="article in articles.data" 
        :key="article.id" 
        class="relative flex flex-col sm:flex-row xl:flex-col items-start h-full">

      <div class="order-1 sm:ml-6 xl:ml-0 flex flex-col flex-1">
        <h3 class="mb-1 text-slate-900 font-semibold dark:text-gray-200">
          <span class="mb-1 block text-sm leading-6 text-green-500" v-if="article.categories[0]"> 
            <a :href="route('category.show', article.categories[0].slug)">{{ article.categories[0].name ?? 'Uncategorized' }}</a>
          </span>
         <a :href="route('article.show', article.slug)">{{ article.title }}</a>
        </h3>

        <div class="prose prose-slate prose-sm text-slate-600 flex-1"
             v-html="article.excerpt.length < 100 ? article.excerpt : article.excerpt.slice(0, 150) + '...'">
        </div>

        <a :href="`/article/read/${article.slug}`"
           class="group inline-flex items-center h-9 rounded-full text-sm font-semibold whitespace-nowrap px-3 focus:outline-none focus:ring-2 bg-slate-100 text-slate-700 hover:bg-slate-200 hover:text-slate-900 focus:ring-slate-500 mt-6 self-start">
          Baca selengkapnya
          <svg class="overflow-visible ml-3 text-slate-300 group-hover:text-slate-400"
               width="3" height="6" viewBox="0 0 3 6" fill="none" stroke="currentColor" stroke-width="2"
               stroke-linecap="round" stroke-linejoin="round">
            <path d="M0 0L3 3L0 6"></path>
          </svg>
        </a>
      </div>
      <a :href="route('article.show', article.slug)">
        <img :src="article.cover ? `/storage/${article.cover}` : 'https://via.placeholder.com/400x200'"
             :alt="article.slug"
             class="mb-6 shadow-md rounded-lg bg-slate-50 w-full sm:w-[17rem] sm:mb-0 xl:mb-6 xl:w-full cursor-pointer"
             width="1216" height="640">
      </a>
    </li>
  </ul>

  <div class="flex justify-center my-8 space-x-2">
    <template v-for="link in articles.links" :key="link.label">
      <button
        v-if="link.url"
        @click="$inertia.visit(link.url)"
        class="px-3 py-1 rounded-md border text-sm"
        :class="{
          'bg-blue-600 text-white border-blue-600': link.active,
          'bg-white text-gray-700 border-gray-300 hover:bg-gray-100': !link.active
        }"
        v-html="link.label"
      />
      <span
        v-else
        class="px-3 py-1 text-gray-400 text-sm"
        v-html="link.label"
      />
    </template>
  </div>
</PageLayout>
</template>
