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

<style>
/* Main Blog Container */
.blog-section {
    max-width: 1400px;
    margin: 0 auto;
    padding: 4rem 2rem;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.blog-header {
    text-align: center;
    margin-bottom: 3rem;
}

.blog-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 1rem;
}

.blog-subtitle {
    font-size: 1.2rem;
    color: #4a5568;
    max-width: 700px;
    margin: 0 auto;
    line-height: 1.6;
}

/* Blog Posts Grid */
.blog-posts {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 2rem;
}

/* Individual Blog Post Card */
.blog-card {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.blog-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
}

.blog-card-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.blog-card-content {
    padding: 1.5rem;
}

.blog-card-category {
    display: inline-block;
    background: #4299e1;
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 50px;
    font-size: 0.8rem;
    font-weight: 600;
    margin-bottom: 0.75rem;
}

.blog-card-title {
    font-size: 1.4rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 0.75rem;
    line-height: 1.3;
}

.blog-card-excerpt {
    color: #4a5568;
    margin-bottom: 1.25rem;
    line-height: 1.6;
}

.blog-card-meta {
    display: flex;
    align-items: center;
    font-size: 0.9rem;
    color: #718096;
}

.blog-card-date {
    margin-right: 1rem;
}

.blog-card-author {
    display: flex;
    align-items: center;
}

.author-avatar {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    margin-right: 0.5rem;
    object-fit: cover;
}

/* View All Button */
.view-all-container {
    text-align: center;
    margin-top: 3rem;
}

.view-all-btn {
    display: inline-block;
    background: #4299e1;
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 50px;
    font-weight: 600;
    text-decoration: none;
    transition: background 0.3s ease;
}

.view-all-btn:hover {
    background: #3182ce;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .blog-section {
        padding: 3rem 1.5rem;
    }

    .blog-title {
        font-size: 2rem;
    }

    .blog-subtitle {
        font-size: 1.1rem;
    }

    .blog-posts {
        grid-template-columns: 1fr;
    }
}
</style>
<template>
    <section class="blog-section">
        <div class="blog-header">
            <h2 class="blog-title dark:!text-gray-300">Choiced <span
                    class="text-green-700 dark:text-green-500">Articles</span></h2>
            <p class="blog-subtitle dark:!text-gray-300">Discover our top insights, stories, and updates to help you
                stay informed and inspired.</p>
        </div>

        <div class="max-w-screen-xl mx-auto p-5 sm:p-10 md:p-16 relative">
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-5">

                <div class="sm:col-span-5" v-if="mainArticle">
                    <a href="#">
                        <div class="bg-cover text-center overflow-hidden" style="min-height: 300px"
                            :style="`background-image: url('/storage/${mainArticle.cover}')`"
                            :title="mainArticle.title"></div>
                    </a>
                    <div
                        class="mt-3  rounded-b lg:rounded-b-none lg:rounded-r flex flex-col justify-between leading-normal">
                        <div>
                            <a href="#"
                                class="text-xs text-indigo-600 uppercase font-medium hover:text-gray-900 transition duration-500 ease-in-out dark:text-green-600">
                                {{ mainArticle.categories[0].name || 'Uncategorized' }}
                            </a>
                            <a href="#"
                                class="block text-gray-900 font-bold text-2xl mb-2 hover:text-indigo-600 transition duration-500 ease-in-out dark:text-white">
                                {{ mainArticle.title }}
                            </a>
                            <p class="text-gray-700 text-base mt-2 dark:text-gray-400" v-html="mainArticle.excerpt"></p>
                        </div>
                    </div>
                </div>

  <div class="sm:col-span-7 grid grid-cols-2 lg:grid-cols-3 gap-5">
          <div v-for="article in sideArticles" :key="article.id">
            <a href="#">
              <div
                class="h-40 bg-cover text-center overflow-hidden"
                :style="`background-image: url('/storage/${article.cover}')`"
                :title="article.title"
              ></div>
            </a>
            <a href="#"
              class="text-gray-900 inline-block font-semibold text-md my-2 hover:text-indigo-600 transition duration-500 ease-in-out dark:text-white">
              {{ article.title }}
            </a>
          </div>
        </div>
            </div>
        </div>

    </section>
</template>