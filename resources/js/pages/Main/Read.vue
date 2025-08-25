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
import CommentSection from '@/components/CommentSection.vue';
const page = usePage();
const { article, recent, initialLiked, initialCount, comments, commentsPagination } = page.props;
</script>

<template>
    
    <PageLayout>
        <ArticleContent :article="article" :recent="recent" />
        <CommentSection :article="article" :recent="recent" :initial-liked="initialLiked" :initial-count="initialCount" :comments="comments" :comments-pagination="commentsPagination"/>
    </PageLayout>
</template>