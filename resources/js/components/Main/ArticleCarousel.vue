<script setup lang="ts">
import { Swiper, SwiperSlide } from 'swiper/vue'
import 'swiper/css'
import 'swiper/css/navigation'
import 'swiper/css/pagination'
import { Navigation, Pagination, Autoplay } from 'swiper/modules'
import dayjs from 'dayjs'

const props = defineProps<{
    articles: Array<any>
}>()
</script>

<template>
    <div class="relative mt-20">
        <div class="mx-auto max-w-[80rem] mb-[4rem]">
            <h2 class="text-4xl font-semibold tracking-tight text-gray-900 dark:text-gray-200 sm:text-5xl">
                Best Of The Week
            </h2>
            <p class="mt-2 text-lg leading-8 text-gray-600 dark:text-gray-300">
                Stay informed with ur latest articles and experto opinions.
            </p>
        </div>

        <Swiper :modules="[Navigation, Pagination, Autoplay]" :slides-per-view="1" :space-between="20" pagination
            autoplay class="max-w-[80rem] mx-auto rounded-3xl max-lg:rounded-none">
            <SwiperSlide v-for="(article, i) in articles" :key="i">
                <a :href="route('article.show', article.slug)">
                    <div
                        class="relative block rounded-3xl max-lg:rounded-none bg-white text-white shadow-secondary-1 dark:bg-surface-dark group overflow-hidden max-h-[540px]">
                        <img class="group-hover:scale-[110%] ease-in-out duration-500 w-full object-cover"
                            :src="article.cover ? `/storage/${article.cover}` : 'https://picsum.photos/id/1018/1920/1080'"
                            alt="thumbnail" />
                        <Tag as="a" severity="contrast" :value="article.category" class="absolute top-0"></Tag>
                        <div
                            class="absolute bottom-0 rounded-b-2xl p-6 bg-black/30 backdrop-blur-lg w-full opacity-0 group-hover:opacity-100 ease-in-out duration-300">
                            <h5 class="mb-2 text-xl font-medium leading-tight">
                                {{ article.title }}
                            </h5>
                            <p class="mb-4 text-base" v-html="article.excerpt.length > 250 ? article.excerpt.slice(0, 250) + '...' : article.excerpt">
                            </p>
                            <div class="text-base flex flex-row justify-between">
                                <div class="flex flex-row items-center *:mr-2">
                                    <a :href="route('profile.show', article.user.username)" class="flex items-center">
                                        <img :src="article.user.avatar ? `/storage/${article.user.avatar}` : 'https://ui-avatars.com/api/?name=' + article.user.username"
                                            alt="avatar" class="rounded-full size-7  mr-2" />
                                        <small>{{ article.user.username }}</small></a>
                                    <small>{{ dayjs(article.created_at).format('MMMM DD YYYY') }}</small>
                                </div>
                                <div>
                                    <span class="flex items-center text-[12px] !text-gray-400 font-mono gap-4"
                                        :style="{ color: article.liked ? 'red' : 'gray' }">
                                        <span class="flex items-center">
                                            <i class="pi pi-comment mr-1"></i> {{ article.comments.length ?? 0 }}
                                        </span>
                                        <span class="flex items-center">
                                            <i
                                                :class="article.liked ? 'pi pi-heart-fill mr-1' : 'pi pi-heart mr-1'"></i>
                                            {{ article.likes.length ?? 0 }}
                                        </span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </SwiperSlide>
        </Swiper>
    </div>
</template>
