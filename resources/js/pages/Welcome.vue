<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import PageNav from '@/components/PageNav.vue';
import LatestArticles from '@/components/LatestArticles.vue';
import PageLayout from '@/layouts/PageLayout.vue';
const { articles, tags, categories, users, title } =usePage().props;
</script>

<template>
<PageLayout>
    <LatestArticles :articles="articles"/>
</PageLayout>
</template>
