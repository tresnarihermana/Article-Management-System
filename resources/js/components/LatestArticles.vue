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
<ArticlePostCard :articles="articles"/>

</template>