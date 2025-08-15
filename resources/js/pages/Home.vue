<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import PageNav from '@/components/PageNav.vue';
import PageLayout from '@/layouts/PageLayout.vue';
import MainHeroSection from '@/components/MainHeroSection.vue';
import MainPinnedArticles from '@/components/MainPinnedArticles.vue';
import LatestArticles from '@/components/LatestArticles.vue';
import ThemeMode from '@/components/ThemeMode.vue';
import ArticleList from '@/components/ArticleList.vue';
import LatestArticleCarousel from '@/components/LatestArticleCarousel.vue';
const { articles, tags, categories, users, title } = usePage().props;
</script>

<template>
    <Head title="Home">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <PageLayout>
        <MainHeroSection class="max-w-[2560px] mx-auto relative" />
        <LatestArticleCarousel :articles="articles" class="pt-10" />
        <ArticleList :categories="categories" class="max-w-[1200px] mx-auto relative" />
    </PageLayout>
</template>
