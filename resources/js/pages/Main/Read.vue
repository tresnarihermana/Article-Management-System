<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { shallowRef } from 'vue';
import ArticleContent from '@/components/ArticleContent.vue';
import PageLayout from '@/layouts/PageLayout.vue';
import CommentSection from '@/components/CommentSection.vue';
import ClientOnly from '@/components/ClientOnly.vue';
const page = usePage();
const { article, recent, initialLiked, initialCount, comments, commentsPagination } = page.props;

const section = shallowRef<HTMLElement>();


function scrolltoComment() {
  const el = document.getElementById('comment-section')
  el?.scrollIntoView({ behavior: 'smooth' })
}

</script>

<template>

    <PageLayout>
        <ArticleContent :article="article" :recent="recent" @scrollTo="scrolltoComment()"  />
        <ClientOnly v-if="comments">
            <CommentSection :article="article" :recent="recent" :initial-liked="initialLiked"
                :initial-count="initialCount" :comments="comments" :comments-pagination="commentsPagination" />
        </ClientOnly>
    </PageLayout>
</template>