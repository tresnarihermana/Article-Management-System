<script setup lang="ts">
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Navigation, Pagination } from 'swiper/modules'
import 'swiper/css';
import 'swiper/css/pagination';
import 'swiper/css/navigation';
const props = defineProps<{
  categories: Array<any>,
}>();

</script>

<template>
  <div class="py-6">
    <div class="xl:container mx-auto px-3 sm:px-4 xl:px-2">
      <div class="flex flex-wrap">
        <!-- Konten utama -->
        <div class="w-full lg:w-2/3 overflow-hidden py-10">
          <div v-for="category in categories" :key="category.id" class="w-full py-3">
            <h2 class="text-gray-800 text-2xl font-bold mb-4 dark:text-gray-300">
              <span class="inline-block h-5 border-l-4 border-green-600 mr-2"></span>
              {{ category.name }}
            </h2>

            <Swiper :slides-per-view="3" :space-between="20" :breakpoints="{
              640: { slidesPerView: 1 },
              768: { slidesPerView: 2 },
              1024: { slidesPerView: 3 }
            }" :pagination="{
                dynamicBullets: true,
              }"
              class="mySwiper"
              :navigation="true"
              :modules="[Pagination, Navigation]">
              <SwiperSlide v-for="article in category.articles" :key="article.id" class="hover-img">
                <a :href="route('article.show', article.slug)">
                  <img class="max-w-full w-full mx-auto" :src="`/storage/${article.cover}`" :alt="article.title" />
                </a>
                <div class="py-3">
                  <h3 class="text-lg font-bold leading-tight mb-2">
                    <a :href="route('article.show', article.slug)">{{ article.title }}</a>
                  </h3>
                  <p class="hidden md:block text-gray-600 leading-tight mb-1 dark:text-gray-400"
                    v-html="article.excerpt.length < 120 ? article.excerpt : article.excerpt.slice(0, 120) + '...'"></p>
                  <a class="text-gray-500" :href="route('category.show',category.slug)">
                    <span class="inline-block h-3 border-l-2 border-green-600 mr-2"></span>
                    {{ category.name }}
                  </a>
                </div>
              </SwiperSlide>
            </Swiper>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="w-full lg:w-1/3 lg:pl-8 lg:pt-14 lg:pb-8 order-first lg:order-last pt-10">
          <div class="w-full bg-white dark:bg-zinc-800 sticky top-20">
            <div class="mb-6">
              <div class="p-4 bg-gray-100 dark:bg-zinc-900">
                <h2 class="text-lg font-bold dark:text-green-500">Most Popular</h2>
              </div>
              <ul class="post-number">
                <li v-for="pop in categories.flatMap(cat => cat.articles).slice(0, 5)" :key="pop.id"
                  class="border-b border-gray-100 dark:border-gray-800 dark:hover:bg-gray-700 hover:bg-gray-50">
                  <a class="text-lg font-bold px-6 py-3 flex flex-row items-center" :href="route('article.show', pop.slug)">
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
<style>
.mySwiper .swiper-button-next,
.mySwiper .swiper-button-prev {
  width: 42px;
  height: 42px;
  background: rgba(0, 0, 0, 0.3); /* transparan */
  backdrop-filter: blur(6px); /* efek blur kaca */
  border-radius: 50%;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
  transition: all 0.3s ease;
  color: white; /* warna icon */
}

.mySwiper .swiper-button-next::after,
.mySwiper .swiper-button-prev::after {
  font-size: 16px; /* ukuran icon panah */
  font-weight: bold;
}

.mySwiper .swiper-button-next:hover,
.mySwiper .swiper-button-prev:hover {
  background: rgba(34, 197, 94, 0.8); /* hijau Tailwind (#22c55e) */
  transform: scale(1.1);
}
</style>
