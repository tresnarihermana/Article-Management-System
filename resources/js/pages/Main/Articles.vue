<script setup lang="ts">
import PageLayout from '@/layouts/PageLayout.vue';
import ArticleList from '@/components/ArticleList.vue';
import { Head, usePage } from '@inertiajs/vue3';
import SearchBar from '@/components/SearchBar.vue';
import SlidableCategories from '@/components/SlidableCategories.vue';
const page = usePage()
const {categorized, categories} = page.props
</script>

<template>
      <Head title="Articles">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>>
  <PageLayout>
    <SlidableCategories :categories="categories" class="max-w-[1200px] top-15"/>
    <ArticleList :categorized="categorized">
    </ArticleList>
  </PageLayout>
</template>
