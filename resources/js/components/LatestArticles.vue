<script setup lang="ts">
import { useInitials } from '@/composables/useInitials';
import ArticlePostCard from './ArticlePostCard.vue';
import { computed } from 'vue';
const props = defineProps({
    articles: Object,
})
const { getInitials } = useInitials();
const pinnedArticles = computed(() => {
    return props.articles.filter(a => a.is_pinned)
})
const mainArticle = computed(() => pinnedArticles.value[0])
const sideArticles = computed(() => pinnedArticles.value.slice(1))
</script>

<template>
    <div class="text-center mb-12">
      <h2 class="text-4xl md:text-5xl font-bold text-gray-800 dark:text-gray-300 mb-4">
        Latest <span class="text-green-700 dark:text-green-500">Articles</span>
      </h2>
      <p class="text-gray-600 dark:text-gray-300 max-w-xl mx-auto text-base md:text-lg leading-relaxed">
        Discover our latest insights, stories, and updates to help you stay informed and inspired.
      </p>
    </div>
    <ArticlePostCard :articles="articles" />

</template>