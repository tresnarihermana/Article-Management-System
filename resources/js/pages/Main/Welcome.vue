<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import PageLayout from '@/layouts/PageLayout.vue';
import MainHeroSection from '@/components/WelcomeHeroSection.vue';
import ArticleList from '@/components/ArticleList.vue';
import LatestArticleCarousel from '@/components/LatestArticleCarousel.vue';
const { articles, tags, categories, users, title } = usePage().props;
</script>

<template>
    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <PageLayout>
        <MainHeroSection class="mx-auto relative max-md:pt-10" />
    </PageLayout>
</template>
