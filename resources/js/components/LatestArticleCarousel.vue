<script lang="ts" setup>
import { ref, onMounted, onBeforeUnmount, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';

interface Article {
  id: number;
  title: string;
  excerpt?: string;
  slug: string;
  cover: string;
}

interface Slide {
  title: string;
  description: string;
  buttonText: string;
  buttonUrl: string;
  image: string;
}

const props = defineProps({
  articles: Array<any>
});

const slides = ref<Slide[]>(props.articles.map((article: Article) => ({
  title: article.title,
  description: article.excerpt || article.title,
  buttonText: "Read More",
  buttonUrl: route('article.show', article.slug),
  image: `/storage/${article.cover}`
})));

const currentSlide = ref(0);
const autoplay = ref(true);
let interval: number;

const next = () => { currentSlide.value = (currentSlide.value + 1) % slides.value.length; };
const prev = () => { currentSlide.value = (currentSlide.value - 1 + slides.value.length) % slides.value.length; };
const goTo = (index: number) => { currentSlide.value = index; };
const startAutoplay = () => { interval = window.setInterval(() => { if (autoplay.value) next(); }, 5000); };
const stopAutoplay = () => { clearInterval(interval); };
const replaceBrokenImage = (event: Event) => {
  const target = event.target as HTMLImageElement;
  target.src = 'https://picsum.photos/id/1018/1920/1080';
};

onMounted(() => { startAutoplay(); });
onBeforeUnmount(() => { stopAutoplay(); });
</script>

<template>
  <div class="relative overflow-hidden" @mouseenter="autoplay = false" @mouseleave="autoplay = true">
    <div class="max-w-[1200px] mx-auto relative">
      <div v-for="(slide, index) in slides" :key="index" v-show="currentSlide === index"
        class="transition-opacity duration-700">
        <div class="w-full aspect-[2.54/1] relative">
          <img :src="slide.image" :alt="slide.title" class="w-full h-full object-cover opacity-90 object-top"
            @error="replaceBrokenImage" loading="lazy" />
          <div class="absolute inset-0 container mx-auto px-4 py-6 flex items-center">
            <div class="max-w-xl text-white transition-all duration-600" :class="{
              'translate-x-0 opacity-100': currentSlide === index,
              'translate-x-6 opacity-0': currentSlide !== index
            }">
              <h2 class="text-3xl md:text-4xl font-bold mb-3">{{ slide.title }}</h2>
              <p class="text-lg md:text-xl mb-6"
                v-html="slide.description.length > 120 ? slide.description.slice(0, 160) + '...' : slide.description"></p>
              <a :href="slide.buttonUrl"
                class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-base md:text-lg font-semibold transition-colors">
                {{ slide.buttonText }}
              </a>
            </div>
          </div>
        </div>
      </div>

      <button @click="prev"
        class="absolute left-2 md:left-4 top-1/2 -translate-y-1/2 bg-black/50 hover:bg-black/70 text-white rounded-full w-8 h-8 md:w-10 md:h-10 flex items-center justify-center z-10 transition-all">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 md:h-5 md:w-5" fill="none" viewBox="0 0 24 24"
          stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
      </button>
      <button @click="next"
        class="absolute right-2 md:right-4 top-1/2 -translate-y-1/2 bg-black/50 hover:bg-black/70 text-white rounded-full w-8 h-8 md:w-10 md:h-10 flex items-center justify-center z-10 transition-all">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 md:h-5 md:w-5" fill="none" viewBox="0 0 24 24"
          stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
      </button>

      <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex space-x-2 z-10">
        <button v-for="(slide, index) in slides" :key="index" @click="goTo(index)"
          class="w-2 h-2 md:w-3 md:h-3 rounded-full transition-all" :class="{
            'bg-white w-4 md:w-5': currentSlide === index,
            'bg-white/50': currentSlide !== index
          }"></button>
      </div>
    </div>
  </div>
</template>
