<script setup lang="ts">
import { Navigation } from 'swiper/modules';
import { SwiperSlide, Swiper } from 'swiper/vue';
import 'swiper/css';

const props = defineProps({
    categories: Array,
});

// Calculate slide width to show exactly 6 items
const slideWidth = 100 / 12;
</script>

<template>
    <div class="relative mx-auto border-b border-green-400 py-3">
        <button
            class="custom-prev absolute left-2 top-1/2 -translate-y-1/2 z-10 w-8 h-8 flex items-center justify-center rounded-full bg-white dark:bg-neutral-800 shadow-[0_1px_3px_rgba(0,0,0,0.08)] hover:bg-gray-100 dark:hover:bg-green-600 dark:hover:text-red-600 disabled:opacity-30 disabled:cursor-not-allowed">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-[14px] w-[14px] text-[#6B6B6B] dark:!text-white" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
        </button>
        <button
            class="custom-next absolute right-2 top-1/2 -translate-y-1/2 z-10 w-8 h-8 flex items-center justify-center rounded-full bg-white dark:bg-neutral-800 shadow-[0_1px_3px_rgba(0,0,0,0.08)] hover:bg-gray-100 dark:hover:bg-green-600 disabled:opacity-30 disabled:cursor-not-allowed">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-[14px] w-[14px] text-[#6B6B6B] dark:!text-white" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
        </button>

        <Swiper ref="swiperRef" :slides-per-view="2" :breakpoints="{
            640: { slidesPerView: 3 },
            768: { slidesPerView: 6 },
            1024: { slidesPerView: 12 }
        }" :space-between="0" :modules="[Navigation]" :navigation="{
                prevEl: '.custom-prev',
                nextEl: '.custom-next',
            }" class="!overflow-hidden mx-auto max-w-[1192px] px-[52px]">
            <SwiperSlide v-for="c in categories" :key="c.name" :style="{ width: `${slideWidth}%` }">
                <a :href="`articles/category/` + c.slug"
                    class="block px-3 py-[6px] text-[13.5px] leading-[20px] font-medium rounded-full transition-colors whitespace-nowrap text-center"
                    :class="{
                        'bg-[#191919] text-white': c.active,
                        'text-[#6B6B6B] hover:bg-[#F5F5F5]': !c.active
                    }">
                    {{ c.name }}
                </a>
            </SwiperSlide>
        </Swiper>
    </div>
</template>

<style>
.swiper {
    overflow: hidden !important;
}

.swiper-slide {
    display: flex;
    justify-content: center;
}

/* Medium's exact color for active state */
.bg-\[\#191919\] {
    background-color: #191919 !important;
}

/* Medium's exact color for inactive text */
.text-\[\#6B6B6B\] {
    color: #6B6B6B !important;
}

/* Medium's exact hover background */
.hover\:bg-\[\#F5F5F5\]:hover {
    background-color: #F5F5F5 !important;
}
</style>