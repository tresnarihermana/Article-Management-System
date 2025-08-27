<script setup lang="ts">
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Navigation, Pagination } from 'swiper/modules'
import 'swiper/css';
import 'swiper/css/pagination';
import 'swiper/css/navigation';
import Button from 'primevue/button';
import { Inertia } from '@inertiajs/inertia';
import { computed } from 'vue';
import { WhenVisible } from '@inertiajs/vue3';
const props = defineProps<{
  categorized: Array<any>,
  ArticlesPagination: Object,
}>();

</script>

<template>
    <div class="py-6">
      <div class="xl:container mx-auto px-3 sm:px-4 xl:px-2">
        <div class="flex flex-wrap">
  
          <div class="w-full lg:w-2/3 overflow-hidden py-10">
            <div v-for="category in categorized" :key="category.id" class="w-full py-6">
              <div class="justify-between flex">
              <h2 class="text-gray-800 text-2xl font-extrabold mb-6 dark:text-gray-200 flex items-center">
                <span class="inline-block h-6 w-1.5 rounded-full bg-green-500 mr-3"></span>
                {{ category.name }}
              </h2>
              <a :href="route('category.show', category.slug)" class="text-gray-800 hover:text-green-400 hover:underline text-md font-extrabold mb-6 dark:text-gray-500 dark:hover:text-green-500 flex items-center">
                See More >
              </a>
            </div>

              <Swiper :slides-per-view="3" :space-between="24" :breakpoints="{
                640: { slidesPerView: 1 },
                768: { slidesPerView: 2 },
                1024: { slidesPerView: 3 }
              }" :pagination="{ dynamicBullets: true }" :navigation="true" :lazy="true"
                :modules="[Pagination, Navigation]" class="mySwiper">
                <SwiperSlide v-for="article in category.articles" :key="article.id"
                  class="group transition duration-300">
                  <a :href="route('article.show', article.slug)"
                    class="block bg-white dark:bg-zinc-800 rounded-2xl shadow-md hover:shadow-xl overflow-hidden transition flex flex-col h-[360px]">
                    <img class="w-full h-40 object-cover rounded-t-2xl group-hover:scale-105 transition duration-500"
                      :src="`/storage/${article.cover}`" :alt="article.title" loading="lazy" />

                    <div class="flex flex-col flex-grow p-4">
                      <h3
                        class="text-lg font-bold leading-tight mb-2 group-hover:text-green-600 transition line-clamp-2">
                        {{ article.title }} 
                      </h3>
                      <p class="hidden md:block text-gray-600 dark:text-gray-400 text-sm mb-2 flex-grow line-clamp-3"
                        v-html="article.excerpt.length < 100 ? article.excerpt : article.excerpt.slice(0, 100) + '...'">
                      </p>

                      <a class="mt-auto inline-flex items-center text-sm text-green-600 font-medium hover:underline"
                        :href="route('profile.show', article.user.username)">
                        <span class="inline-block h-2 w-2 rounded-full bg-green-600 mr-2"></span>
                      by {{ article.user.username }}
                      </a>
                    </div>
                  </a>
                </SwiperSlide>

                <SwiperSlide class="group transition duration-300">
                  <a :href="route('category.show', category.slug)"
                    class="block bg-white dark:bg-zinc-800 rounded-2xl shadow-md hover:shadow-xl overflow-hidden transition flex items-center justify-center h-[360px]">
                    <div class="flex flex-col items-center justify-center text-center p-6">
                      <div
                        class="w-16 h-16 flex items-center justify-center rounded-full bg-green-100 dark:bg-green-900 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg"
                          class="h-8 w-8 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24"
                          stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5l7 7-7 7" />
                        </svg>
                      </div>
                      <h3 class="text-lg font-bold text-green-600">Lihat Lebih Lanjut</h3>
                      <p class="text-gray-600 dark:text-gray-400 text-sm mt-2">
                        Jelajahi semua kategori lainnya
                      </p>
                    </div>
                  </a>
                </SwiperSlide>
              </Swiper>
            </div>
            <slot name="pagination" />
          </div>

          <!-- Sidebar -->
          <slot name="sidebar" />
        </div>
      </div>
    </div>
</template>


<style>
.mySwiper .swiper-button-next,
.mySwiper .swiper-button-prev {
  width: 44px;
  height: 44px;
  background: rgba(0, 0, 0, 0.25);
  backdrop-filter: blur(8px);
  border-radius: 50%;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
  transition: all 0.3s ease;
  color: white;
}

.mySwiper .swiper-button-next::after,
.mySwiper .swiper-button-prev::after {
  font-size: 16px;
  font-weight: bold;
}

.mySwiper .swiper-button-next:hover,
.mySwiper .swiper-button-prev:hover {
  background: rgba(34, 197, 94, 0.9);
  transform: scale(1.15);
  box-shadow: 0 6px 14px rgba(34, 197, 94, 0.5);
}
</style>
