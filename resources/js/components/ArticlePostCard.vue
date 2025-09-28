<script setup lang="ts">
import { getInitials } from '@/composables/useInitials';
import { Swiper, SwiperSlide } from 'swiper/vue';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import SwiperCore, { Navigation, Pagination, Autoplay } from 'swiper';

SwiperCore.use([Navigation, Pagination, Autoplay]);

const props = defineProps({
  articles: Array as () => Array<any>, // pastikan props articles array
});
</script>

<template>
  <div class="max-w-7xl mx-auto py-16 px-4 font-sans">

    <Swiper
      :slides-per-view="1"
      :space-between="20"
      :loop="true"
      :navigation="true"
      :pagination="{ clickable: true }"
      :autoplay="{ delay: 3000 }"
      :breakpoints="{
        640: { slidesPerView: 1 },
        768: { slidesPerView: 2 },
        1024: { slidesPerView: 3 }
      }"
      class="mySwiper"
    >
      <SwiperSlide v-for="article in articles" :key="article.id">
        <article
          class="bg-white dark:bg-black/5 rounded-lg shadow-md overflow-hidden transition duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-lg"
        >
          <a :href="route('article.show', { id: article.id, slug: article.slug })
">
            <img
              :src="article.cover
                    ? `/storage/${article.cover}`
                    : `https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=1470&q=80`"
              :alt="article.slug"
              class="w-full h-52 object-cover"
            />

            <div class="p-6">
              <div class="flex flex-wrap gap-1 mb-3">
                <span
                  v-for="categories in article.categories"
                  :key="categories.name"
                  class="inline-block bg-blue-500 text-white text-xs font-semibold px-3 py-1 rounded-full"
                >
                  {{ categories.name }}
                </span>
              </div>

              <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-3 leading-snug">
                {{ article.title }}
              </h3>

              <p class="text-gray-600 dark:text-gray-400 mb-4 leading-relaxed" v-html="article.excerpt.slice(0, 150) + (article.excerpt.length > 150 ? '...' : '')"></p>

              <div class="flex items-center text-sm text-gray-500">
                <span class="mr-4">June 15, 2023</span>
                <div class="flex items-center">
                  <img
                    :src="article.user.avatar
                          ? article.user.avatar_url
                          : 'https://ui-avatars.com/api/?name=' +
                            getInitials(article.user.username) +
                            '&background=random'"
                    alt="user avatar"
                    class="w-6 h-6 rounded-full object-cover mr-2"
                  />
                  {{ article.user.name }}
                </div>
              </div>
            </div>
          </a>
        </article>
      </SwiperSlide>
    </Swiper>

  </div>
</template>
