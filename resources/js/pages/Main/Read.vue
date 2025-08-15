<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import ArticleContent from '@/components/ArticleContent.vue';
import Button from 'primevue/button';
import { router } from '@inertiajs/vue3';
import PageLayout from '@/layouts/PageLayout.vue';
const page = usePage();
const { article, recent } = page.props;
</script>

<template>
    
    <PageLayout>
        <ArticleContent :article="article" :recent="recent"/>
    </PageLayout>
</template>