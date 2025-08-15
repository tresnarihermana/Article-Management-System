<script setup lang="ts">
import { useInitials } from '@/composables/useInitials';
import { Head } from '@inertiajs/vue3';
const props = defineProps({
    article: Object,
    categories: Array,
    recent: Array<any>,
})
const { getInitials } = useInitials();
</script>

<template>
        <Head :title="article?.title">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div class="flex flex-col py-14">
        <div class="bg-gray-100 pt-8 dark:bg-zinc-900">
            <div class="container mx-auto px-4">
                <h1 class="text-4xl font-bold text-gray-800 dark:text-gray-200 mb-4">
                    {{ article.title }}
                </h1>

                <div class="flex items-center text-gray-600 dark:text-gray-300 mb-4">
                    <img :src="article.user.avatar
                        ? `/storage/${article.user.avatar}`
                        : `https://ui-avatars.com/api/?name=${getInitials(article.user.username)}&background=random`"
                        alt="user avatar" class="w-8 h-8 rounded-full object-cover mr-2" />
                    <span>
                        by
                        <a :href="`/author/${article.user.username}`" class="hover:text-green-400 font-medium">
                            {{ article.user.name }}
                        </a>
                    </span>
                </div>

                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500 dark:text-gray-400 mb-6">
                    <div>
                        <span class="font-semibold">Category: </span>
                        <template v-for="(category, index) in article.categories" :key="category.id">
                            <a :href="route('category.show', category.slug)" class="hover:text-green-400">
                                {{ category.name }}
                            </a>
                            <span v-if="index < article.categories.length - 1">,</span>
                        </template>
                    </div>

                    <div>
                        <span class="font-semibold">Tags: </span>
                        <template v-for="(tag, index) in article.tags" :key="tag.id">
                            <a :href="`/tag/${tag.slug}`" class="hover:text-green-400">
                                {{ tag.name }}
                            </a>
                            <span v-if="index < article.tags.length - 1">, </span>
                        </template>
                    </div>

                    <div>
                        <span class="font-semibold">Views:</span> {{ article.views }}
                    </div>

                    <div>
                        <span class="font-semibold">Hits:</span> {{ article.hits }}
                    </div>

                    <div>
                        <span class="font-semibold">Likes:</span> {{ article.likes }}
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white py-8 dark:bg-zinc-800">
            <div class="container mx-auto px-4 flex flex-col md:flex-row">
                <div class="w-full md:w-3/4 px-4">
                    <img :src="'/storage/' + article.cover" alt="Blog Featured Image" class="mb-8">
                    <div class="prose max-w-none dark:text-gray-200 lg:text-lg max-sm:text-sm" v-html="article.body">

                    </div>
                </div>
                <div class="w-full md:w-1/4 px-4 space-y-6">
                    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-4 sticky top-20">
                        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-4 md:text-lg">Recent Posts</h2>
                        <div v-for="article in recent" :key="article.id" class="mb-4">
                            <a :href="route('article.show', article.slug)" class="block group">
                                <img :src="`/storage/` + article.cover" alt="cover"
                                    class="w-full object-cover rounded-lg mb-2 group-hover:opacity-80 transition aspect-16/9" />
                                <h3
                                    class="text-gray-700 dark:text-gray-200 font-medium group-hover:text-green-600 md:text-xs">
                                    {{ article.title }}
                                </h3>
                            </a>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>
</template>