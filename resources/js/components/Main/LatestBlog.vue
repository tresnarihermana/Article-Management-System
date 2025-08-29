<script setup lang="ts">
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import dayjs from 'dayjs'

const props = defineProps<{
    articles: Array<any>
}>()

const limit = ref(6)
const liked = ref(false)

const visibleArticles = computed(() => {
    return props.articles.slice(0, limit.value)
})

const loadMore = () => {
    if (limit.value === 6) {
        limit.value = 18
    } else {
        router.visit(route('articles.list'))
    }
}
</script>

<template>
    <div class="py-12 sm:py-24" v-if="props.articles.length > 0">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl lg:mx-0">
                <h2 class="text-4xl font-semibold tracking-tight text-zinc-900 dark:text-gray-200 sm:text-5xl">
                    Latest blog posts
                </h2>
                <p class="mt-2 text-lg leading-8 text-gray-600 dark:text-gray-300">
                    Stay informed with our latest articles and expert opinions.
                </p>
            </div>

            <div class="mx-auto mt-16 grid max-w-2xl grid-cols-1 gap-x-8 gap-y-16 
               lg:mx-0 lg:mt-20 lg:max-w-none lg:grid-cols-6">
                <article v-for="article in visibleArticles" :key="article.id"
                    class="flex flex-col items-start justify-between lg:col-span-2 relative">
                    <a :href="route('article.show', article.slug)">
                        <div
                            class="absolute ml-3 mt-2 rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600 hover:bg-gray-200 z-10">
                            <a :href="route('category.show', article.categories[0].slug)">
                                {{ article.categories[0].name }}
                            </a>
                        </div>

                        <img :src="article.cover ? `/storage/${article.cover}` : 'https://picsum.photos/id/1018/1920/1080'"
                            alt="" class="rounded-2xl hover:brightness-90" />

                        <div class="mt-5 flex items-center gap-x-2 text-sm text-gray-500 dark:text-gray-400 w-full">
                            <time :datetime="article.created_at">{{
                                dayjs(article.created_at).format('MMMM DD')
                                }}</time>
                            <span aria-hidden="true">•</span>
                            <p>7 mins read</p>
                            <span class="flex justify-end items-end flex-1 text-[12px] !text-gray-400 font-mono gap-4"
                                :style="{ color: liked ? 'red' : 'gray' }">
                                <span class="flex items-center">
                                    <i class="pi pi-comment mr-1"></i>
                                    {{ article.comments ? article.comments.length : 0 }}
                                </span>
                                <span class="flex items-center">
                                    <i :class="liked ? 'pi pi-heart-fill mr-1' : 'pi pi-heart mr-1'"></i>
                                    {{ article.likes ? article.likes.length : 0 }}
                                </span>

                            </span>
                        </div>

                        <div class="group relative mt-3">
                            <h3
                                class="text-lg font-semibold leading-6 text-gray-900 dark:text-gray-200 group-hover:text-gray-600">
                                <span class="absolute inset-0"></span>
                                {{ article.title }}
                            </h3>
                            <p class="mt-5 line-clamp-2 text-sm leading-6 text-gray-600 dark:text-gray-300"
                                v-html="article.excerpt"></p>
                        </div>

                        <div class="relative mt-8 flex justify-start gap-x-2 ">
                            <div class="flex-none">
                                <span class="relative rounded-full">
                                    <span aria-hidden="true"
                                        class="absolute inset-0 h-10 w-10 rounded-full ring-1 ring-inset ring-gray-900/10"></span>
                                    <img alt="" class="h-10 w-10 rounded-full object-cover" :src="article.user.avatar
                                            ? `/storage/${article.user.avatar}`
                                            : `https://ui-avatars.com/api/?name=${article.user.username}`
                                        " />
                                </span>
                            </div>
                            <div class="text-sm">
                                <p class="font-semibold text-gray-900 dark:text-gray-200">
                                    <a :href="route('profile.show', article.user.username)">
                                        <span class="absolute inset-0"></span>
                                        {{ article.user.username }}
                                    </a>
                                </p>
                                <p class="text-gray-600 dark:text-gray-300">Senior Product Manager</p>
                            </div>
                        </div>
                    </a>
                </article>
            </div>

            <div class="mt-10 flex justify-center" v-if="props.articles.length > 6">
                <button @click="loadMore" class="px-6 py-3 rounded-lg bg-gray-900 text-white hover:bg-gray-700">
                    {{ limit === 6 ? 'Load More' : 'See More' }}
                </button>
            </div>
        </div>
    </div>
</template>
