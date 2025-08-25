<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import PageLayout from '@/layouts/PageLayout.vue';
import ArticleList from '@/components/ArticleList.vue';
import HeroSection from '@/components/HeroSection.vue';
import SlidableCategories from '@/components/SlidableCategories.vue';
import PopularSectionSidebar from '@/components/PopularSectionSidebar.vue';
import Button from 'primevue/button';
const { categorized, categories, popArticles } = usePage().props;
</script>
<template>

    <Head title="Home">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <PageLayout>
        <HeroSection />
        <SlidableCategories :categories="categories" class="max-w-[1200px]" />
        <ArticleList :categorized="categorized" class="max-w-[1200px] mx-auto relative">

            <template #pagination>
                <div class="w-full flex justify-center py-6">
                    <Button as="a" :href="route('articles.list')" label="See More" severity="info" class="mx-auto" />
                </div>
            </template>
            <template #sidebar>
                <PopularSectionSidebar :popArticles="popArticles"></PopularSectionSidebar>
            </template>
        </ArticleList>
    </PageLayout>
</template>
