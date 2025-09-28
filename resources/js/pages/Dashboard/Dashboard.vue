<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
import Swal from 'sweetalert2';
import { Swiper, SwiperSlide } from 'swiper/vue';
import 'swiper/css';
import StatsBarChart from '@/components/StatsBarChart.vue';
import StatsSplineChart from '@/components/StatsSplineChart.vue';
import StatsArticleRank from '@/components/StatsArticleRank.vue';

const page = usePage();
const props = defineProps<{
    categories: { name: string; total_views: number }[]
    articles: { date: string, total_views: number, total_hits: number }[]
    topArticles: {
        title: string
        category: string
        views: number
        percentage: number
    }[]
    stats: {
        title: string
        value: number | string
        percentage: string
        trend: "up" | "down"
        vs: string
        icon: string
        color: string
    }[]
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
];

const flash = page.props?.flash?.message;
if (flash) {
    let timerInterval;
    Swal.fire({
        title: "Selamat Datang",
        icon: "success",
        html: "Login Berhasil dilaksanakan",
        timer: 1000,
        timerProgressBar: true,
        didOpen: () => {
            const timer = Swal.getPopup().querySelector("b");
            timerInterval = setInterval(() => {
                if (timer) timer.textContent = `${Swal.getTimerLeft()}`;
            }, 100);
        },
        willClose: () => {
            clearInterval(timerInterval);
        }
    })
}
</script>

<template>

    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-4 p-4 max-w-[1200px] relative mx-auto">

            <div class="w-full">
                <Swiper :slides-per-view="1" :space-between="16" :breakpoints="{
                    640: { slidesPerView: 2 },
                    1024: { slidesPerView: 3 },
                    1440: { slidesPerView: 4 }
                }">
                    <SwiperSlide v-for="(stat, i) in props.stats" :key="i">
                        <div
                            class="bg-white dark:bg-transparent rounded-xl shadow-sm border border-gray-200 dark:border-zinc-800 p-6 hover:shadow-md transition-shadow h-full">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                                        {{ stat.title }}
                                    </p>
                                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">
                                        {{ stat.value }}
                                    </p>
                                    <div class="flex items-center mt-2">
                                        <span :class="[
                                            stat.trend === 'up' ? 'text-green-600' : 'text-red-600',
                                            'text-sm font-medium flex items-center'
                                        ]">
                                            <i
                                                :class="['pi', stat.trend === 'up' ? 'pi-arrow-up' : 'pi-arrow-down', 'mr-1']"></i>
                                            {{ stat.percentage }}
                                        </span>
                                        <span class="text-gray-500 dark:text-gray-400 text-sm ml-2">
                                            {{ stat.vs }}
                                        </span>
                                    </div>
                                </div>
                                <div class="w-12 h-12 rounded-lg flex items-center justify-center dark:text-gray-200 dark:bg-transparent dark:bg-zinc-800" :class="stat.color">
                                    <i :class="['pi', stat.icon, 'text-xl']"></i>
                                </div>
                            </div>
                        </div>
                    </SwiperSlide>
                </Swiper>
            </div>

            <!-- stats bawah -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full">
    <div
        class="break-inside-avoid relative rounded-xl border border-sidebar-border/70 dark:border-sidebar-border min-h-[60vh] sm:min-h-[50vh] md:min-h-[40vh]">
        <StatsArticleRank :articles="props.topArticles" />
    </div>
    <div
        class="break-inside-avoid relative rounded-xl border border-sidebar-border/70 dark:border-sidebar-border min-h-[60vh] sm:min-h-[50vh] md:min-h-[40vh]">
          <PlaceholderPattern/>
    </div>
    <div
        class="break-inside-avoid relative rounded-xl border border-sidebar-border/70 dark:border-sidebar-border min-h-[60vh] sm:min-h-[50vh] md:min-h-[40vh]">
           <PlaceholderPattern/>
    </div>
    <div
        class="break-inside-avoid relative rounded-xl border border-sidebar-border/70 dark:border-sidebar-border min-h-[60vh] sm:min-h-[50vh] md:min-h-[40vh]">
           <PlaceholderPattern/>
    </div>
</div>

        </div>
    </AppLayout>
</template>
