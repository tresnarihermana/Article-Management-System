<script setup lang="ts">
import { Head, usePage, router } from '@inertiajs/vue3';
import PageLayout from '@/layouts/PageLayout.vue';
import HeroSection from '@/components/HeroSection.vue';
import SlidableCategories from '@/components/SlidableCategories.vue';
import { ref, computed } from 'vue';
import Footer from '@/components/Main/Footer.vue';
import dayjs from 'dayjs';
import ArticleCarousel from '@/components/Main/ArticleCarousel.vue';
import LatestInsight from '@/components/Main/LatestInsight.vue';
import LatestBlog from '@/components/Main/LatestBlog.vue';

const { categories, articles } = usePage().props;
</script>

<template>
  <Head title="Home">
    <link rel="preconnect" href="https://rsms.me/" />
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
  </Head>

  <PageLayout>
    <HeroSection /> 
    <SlidableCategories :categories="categories" class="max-w-[80rem]" />
    <ArticleCarousel :articles="articles" v-if="articles.length > 0" />
    <LatestBlog :articles="articles" />
    <LatestInsight />
    <Footer />
  </PageLayout>
</template>
