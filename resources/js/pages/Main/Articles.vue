<script setup lang="ts">
import PageLayout from '@/layouts/PageLayout.vue';

const props = defineProps<{
  articles: Array<any>,
  categories: Array<any>,
}>();
</script>

<template>
  <PageLayout>
    <main id="content">
      <!-- Asian Section -->
      <div class="bg-gray-50 py-6">
        <div class="xl:container mx-auto px-3 sm:px-4 xl:px-2">
          <div class="flex flex-wrap">
            <!-- Left -->
            <div class="w-full lg:w-2/3 overflow-hidden py-10">
              <div class="w-full py-3" v-for="category in categories">
                <h2 class="text-gray-800 text-2xl font-bold">
                  <span class="inline-block h-5 border-l-3 border-red-600 mr-2"></span>{{ category.name }}
                </h2>
              </div>
              <div class="flex flex-wrap -mx-3">
                <!-- Loop Articles -->
                <div 
                  v-for="(article, index) in articles" 
                  :key="article.id" 
                  class="flex-shrink max-w-full w-full sm:w-1/3 px-3 pb-3"
                >
                  <div class="flex flex-row sm:block hover-img">
                    <a :href="'/articles/' + article.slug">
                      <img 
                        class="max-w-full w-full mx-auto" 
                        :src="article.image" 
                        :alt="article.title"
                      >
                    </a>
                    <div class="py-0 sm:py-3 pl-3 sm:pl-0">
                      <h3 class="text-lg font-bold leading-tight mb-2">
                        <a :href="'/articles/' + article.slug">{{ article.title }}</a>
                      </h3>
                      <p class="hidden md:block text-gray-600 leading-tight mb-1">
                        {{ article.excerpt }}
                      </p>
                      <a class="text-gray-500" href="#">
                        <span class="inline-block h-3 border-l-2 border-red-600 mr-2"></span>
                        {{ article.category }}
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Right Sidebar -->
            <div class="w-full lg:w-1/3 lg:pl-8 lg:pt-14 lg:pb-8 order-first lg:order-last">
              <div class="w-full bg-white">
                <div class="mb-6">
                  <div class="p-4 bg-gray-100">
                    <h2 class="text-lg font-bold">Most Popular</h2>
                  </div>
                  <ul class="post-number">
                    <li 
                      v-for="pop in articles.slice(0,5)" 
                      :key="pop.id"
                      class="border-b border-gray-100 hover:bg-gray-50"
                    >
                      <a 
                        class="text-lg font-bold px-6 py-3 flex flex-row items-center" 
                        :href="'/articles/' + pop.slug"
                      >
                        {{ pop.title }}
                      </a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </main>
  </PageLayout>
</template>
