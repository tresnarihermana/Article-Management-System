<script setup lang="ts">
import CategorizedArticle from '@/components/CategorizedArticle.vue';
import PageLayout from '@/layouts/PageLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
const page = usePage()
const {category} = page.props;
</script>
<template>

    <PageLayout>
        <CategorizedArticle :category="category"/>
    </PageLayout>
</template>