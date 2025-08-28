<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import PageLayout from '@/layouts/PageLayout.vue';
import ArticleList from '@/components/ArticleList.vue';
import HeroSection from '@/components/HeroSection.vue';
import SlidableCategories from '@/components/SlidableCategories.vue';
import PopularSectionSidebar from '@/components/PopularSectionSidebar.vue';
import Button from 'primevue/button';
import MainPinnedArticles from '@/components/MainPinnedArticles.vue';
import { computed } from 'vue';
import Footer from '@/components/Main/Footer.vue';
import { Card } from '@/components/ui/card';
import dayjs from 'dayjs';
const { categorized, categories, popArticles, pinnedArticle, articles } = usePage().props;
const allArticles = computed(() => pinnedArticle || articles || []);
</script>
<template>

    <Head title="Home">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <PageLayout>
        <HeroSection />
        <SlidableCategories :categories="categories" class="max-w-[1200px]" />
        <!-- inspired by tailwindcss.com -->

        <ul class="grid grid-cols-6 grid-rows-2 gap-y-10 gap-x-6 items-start pt-8 pb-8 mx-auto relative max-w-[1440px]">

            <li class="relative flex flex-col sm:flex-row items-start col-span-5" v-for="category in categorized">
                <a :href="route('article.show', category.articles[0].slug)"
                    class="flex flex-col sm:flex-row items-start gap-4 w-full no-underline">
                    <div class="mx-15 border-b-2 flex-1">
                        <h3 class="mb-1 text-slate-900 dark:text-gray-300 font-semibold">
                            <span class="mb-1 block text-sm leading-6 text-indigo-500 relative font-light">
                                <a :href="route('profile.show', category.articles[0].user.username)">
                                    <img class="rounded-full size-6 inline-block mr-2"
                                        :src="category.articles[0].user.avatar ? `/storage/${category.articles[0].user.avatar}` : 'https://ui-avatars.com/api/?name=' + category.articles[0].user.username"
                                        alt="">
                                </a>
                                <a :href="route('profile.show', category.articles[0].user.username)"
                                    class="relative inline-block group transition duration-300">
                                    {{ category.articles[0].user.username }}
                                    <span
                                        class="absolute left-0 -bottom-0.5 w-0 group-hover:w-full h-0.5 bg-sky-600 transition-all duration-500"></span>
                                </a>
                                in
                                <a :href="route('category.show', category.slug)"
                                    class="relative inline-block group transition duration-300">
                                    {{ category.name }}
                                    <span
                                        class="absolute left-0 -bottom-0.5 w-0 group-hover:w-full h-0.5 bg-indigo-500 transition-all duration-500"></span>
                                </a>
                            </span>
                            {{ category.articles[0].title }}
                        </h3>

                        <div class="prose prose-slate prose-sm text-slate-600 dark:text-gray-400"
                            v-html="category.articles[0].excerpt">
                        </div>

                        <div class="my-6 flex items-center gap-10 justify-between">
                            <time :datetime="category.articles[0].created_at"
                                class="font-mono text-[12px] text-gray-400">
                                {{ dayjs(category.articles[0].created_at).format('MMMM DD') }}
                            </time>
                            <span class="flex items-center text-[12px] !text-gray-400 font-mono gap-4"
                                :style="{ color: liked ? 'red' : 'gray' }">
                                <span class="flex items-center">
                                    <i class="pi pi-comment mr-1"></i> {{ CommentCount ? CommentCount : '123' }}
                                </span>
                                <span class="flex items-center">
                                    <i :class="liked ? 'pi pi-heart-fill mr-1' : 'pi pi-heart mr-1'"></i> {{ LikeCount ?
                                        LikeCount : '123' }}
                                </span>
                            </span>
                        </div>
                    </div>

                    <img :src="category.articles[0].cover ? `/storage/${category.articles[0].cover}` : ``" alt=""
                        class="shadow-md rounded-lg bg-slate-50 w-[17rem] mb-0 flex-shrink-0" width="1216" height="640">
                </a>
            </li>

            <li class="row-span-full col-start-6 min-h-full bg-teal-400 max-lg:hidden"></li>

        </ul>
        <!-- tailwindflex -->
        <div class="bg-white py-24 sm:py-32">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-2xl lg:mx-0">
                    <h2 class="text-4xl font-semibold tracking-tight text-gray-900 sm:text-5xl">Latest blog posts</h2>
                    <p class="mt-2 text-lg leading-8 text-gray-600">Stay informed with our latest articles and expert
                        opinions.</p>
                </div>
                <div
                    class="mx-auto mt-16 grid max-w-2xl grid-cols-1 gap-x-8 gap-y-16 lg:mx-0 lg:mt-20 lg:max-w-none lg:grid-cols-6">
                    <article class="flex flex-col items-start justify-between lg:col-span-2"
                        v-for="article in articles">
                        <a :href="route('article.show', article.slug)">
                        <a :href="route('category.show', article.categories[0].slug)"
                            class="absolute ml-3 mt-2 rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600 hover:bg-gray-200">{{
                                article.categories[0].name }}</a>
                            <img src="storage/cover/bBEyfSKKtUrUgWa56G2CB47usnV1qQ4QerT6wAEY.jpg" alt=""
                                class="rounded-2xl">
                        <div class="mt-5 flex items-center gap-x-2 text-sm text-gray-500 w-full">
                            <time :datetime="article.created_at">{{ dayjs(article.created_at).format('MMMM DD')
                            }}</time>
                            <span aria-hidden="true">•</span>
                            <p>7 mins read</p>
                            <span class="flex justify-end items-end flex-1 text-[12px] !text-gray-400 font-mono gap-4"
                                :style="{ color: liked ? 'red' : 'gray' }">
                                <span class="flex items-center">
                                    <i class="pi pi-comment mr-1"></i> {{ CommentCount ? CommentCount : '123' }}
                                </span>
                                <span class="flex items-center">
                                    <i :class="liked ? 'pi pi-heart-fill mr-1' : 'pi pi-heart mr-1'"></i> {{ LikeCount ?
                                        LikeCount : '123' }}
                                </span>
                            </span>
                        </div>
                        <div class="group relative mt-3">
                            <h3 class="text-lg font-semibold leading-6 text-gray-900 group-hover:text-gray-600">
                          
                                    <span class="absolute inset-0"></span>
                                    {{ article.title }}
                              
                            </h3>
                            <p class="mt-5 line-clamp-2 text-sm leading-6 text-gray-600" v-html="article.excerpt"></p>
                        </div>
                        <div class="relative mt-8 flex justify-start gap-x-2">
                            <div class="flex-none">
                                <span class="relative rounded-full">
                                    <span aria-hidden="true"
                                        class="absolute inset-0 h-10 w-10 rounded-full ring-1 ring-inset ring-gray-900/10"></span>
                                    <img alt="" class="h-10 w-10 rounded-full object-cover"
                                        :src="article.user.avatar ? `/storage/${article.user.avatar}` : `https://ui-avatars.com/api/?name=${article.user.username}`" />
                                </span>
                            </div>
                            <div class="text-sm">
                                <p class="font-semibold text-gray-900">
                                    <a :href="route('profile.show', article.user.username)">
                                        <span class="absolute inset-0"></span>
                                        {{ article.user.username }}
                                    </a>
                                </p>
                                <p class="text-gray-600">Senior Product Manager</p>
                            </div>
                        </div>
                        </a>
                    </article>

                </div>
            </div>
        </div>
        <ArticleList :categorized="categorized" class="max-w-[1200px] mx-auto relative">
            <template #pagination>
                <div class="w-full flex justify-center py-6">
                    <Button as="a" :href="route('articles.list')" label="See More" severity="info" class="mx-auto" />
                </div>
            </template>
            <template #sidebar>
                <PopularSectionSidebar :popArticles="popArticles"></PopularSectionSidebar>
            </template>
        </ArticleList>
        <Footer />
    </PageLayout>
</template>
