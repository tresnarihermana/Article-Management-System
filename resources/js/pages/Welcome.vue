<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import PageNav from '@/components/PageNav.vue';
import LatestArticles from '@/components/LatestArticles.vue';
const { articles, tags, categories, users, title } =usePage().props;
</script>

<template>

    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <header>
        <PageNav />
    </header>
    <LatestArticles :articles="articles"/>
</template>
