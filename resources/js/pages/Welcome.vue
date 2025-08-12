<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import PageNav from '@/components/PageNav.vue';
import PageLayout from '@/layouts/PageLayout.vue';
import MainHeroSection from '@/components/MainHeroSection.vue';
import MainPinnedArticles from '@/components/MainPinnedArticles.vue';
import LatestArticles from '@/components/LatestArticles.vue';
import ThemeMode from '@/components/ThemeMode.vue';
const { articles, tags, categories, users, title } =usePage().props;
</script>

<template>
<PageLayout>
    <MainHeroSection/>
    <MainPinnedArticles :articles="articles"/>
<LatestArticles :articles="articles"></LatestArticles>
</PageLayout>
</template>
