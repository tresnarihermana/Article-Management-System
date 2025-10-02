<script setup lang="ts">
import { useInitials } from '@/composables/useInitials';
import { Head, router } from '@inertiajs/vue3';
import axios from 'axios';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import Menu from 'primevue/menu';
import Swal from 'sweetalert2';
import { onMounted, ref } from 'vue';
import LikeButton from './LikeButton.vue';
import ShareModal from './Main/ShareModal.vue';
const props = defineProps({
    article: Object,
    categories: Array,
    recent: Array<any>,
});
const { getInitials } = useInitials();

const article = ref(props.article)
const categories = ref(props.categories);
const recent = ref(props.recent);

onMounted(async () => {
    // console.log(article.value)
    const res = await axios.get(`http://localhost:8001/api/article/180183/google-genie3-ai-sakti-yang-bisa-bikin-dunia-game-dari-satu-prompt`);
    // const res = await axios.get(`http://localhost:8001/api/article/${props.article.id}/${props.article.slug}`);
    article.value = {
        id: res.data.id,
        channel: res.data.channel,
        category_id: res.data.category_id,
        title: res.data.title,
        subTitle: res.data.sub_title,
        slug: res.data.url_alias,
        excerpt: res.data.summary.replace(/<[^>]+>/g, ''),
        cover_url: `https://srv1.portal.p-cd.net/850p/${res.data.image}`,
        detail: {
            id: res.data.detail.id,
            body: res.data.detail.content,
        },
        body: res.data.detail.content,
        user: {
            id: res.data.author.id ?? null,
            name: res.data.author.name ?? null,
            username: res.data.author.alias ?? null,
            cover: res.data.author.cover ?? null,
            avatar: res.data.author.avatar ?  `https://srv1.portal.p-cd.net/300x300/avatar/${res.data.author.avatar}` : `https://ui-avatars.com/api/?name=${res.data.author.username}&background=random`,
        }
    };
    
    console.log(article.value);
    console.log(props.article)
});

/**
 * list yang dibutuhin
 * - title
 * - user (name, username, avatar)
 * - published_at
 * - read_time
 * - categories (name, slug)
 * - tags (name, slug)
 * - views
 * - hits
 * - likes_count
 * - likedByUser
 * - cover_url
 * - body (html)
 */

const goToProfile = () => {
    router.get(route('profile.show', article.value.user.username));
};
const emit = defineEmits(['scrollTo']);

const menu = ref();
const menuitems = ref([
    {
        label: 'Options',
        items: [
            {
                label: 'Download as PDF',
                icon: 'pi pi-download',
                command: () => {
                    window.location.href = route('articles.export-pdf', article.value.id);
                },
            },
        ],
    },
]);

const toggle = (event) => {
    menu.value.toggle(event);
};
const report = ref(false);

const reportReason = ref('');
const reportMessage = ref('');

const submitReport = () => {
    if (!reportReason.value) return;

    router.post(
        route('articles.report', article.value.id),
        {
            reason: reportReason.value,
            message: reportMessage.value,
        },
        {
            onSuccess: () => {
                report.value = false;
                reportReason.value = '';
                reportMessage.value = '';
                Swal.fire('Berhasil', 'Laporan sudah dikirim', 'success');
            },
        },
    );
};
</script>

<template>
    <Head :title="article?.title">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div class="flex flex-col py-14">
        <div class="container mx-auto px-4 pt-12">
            <div class="mb-8 flex flex-col gap-8 border-b-2 border-gray-200 pb-6 dark:border-gray-700">
                <div class="max-w-2xl">
                    <h1 class="font-sans text-4xl leading-tight font-extrabold text-gray-900 md:text-5xl dark:text-gray-100">
                        {{ article.title }}
                    </h1>
                </div>

                <div class="flex items-center justify-between text-gray-600 dark:text-gray-400">
                    <div class="flex items-center gap-4">
                        <img
                            :src="
                                article.user.avatar
                                    ? article.user.avatar
                                    : `https://ui-avatars.com/api/?name=${getInitials(article.user.username)}&background=random`
                            "
                            @click="goToProfile"
                            alt="user avatar"
                            class="h-12 w-12 cursor-pointer rounded-full border-2 border-green-500 object-cover"
                        />
                        <div>
                            <a
                                :href="route('profile.show', article.user.username)"
                                class="text-lg font-bold transition-colors duration-200 hover:text-green-500"
                            >
                                {{ article.user.name }}
                            </a>
                            <div class="text-sm text-gray-500 dark:text-gray-500">
                                <p>{{ article.published_at }} &bull; {{ article.read_time }} min read</p>
                            </div>
                        </div>
                    </div>

                    <div class="hidden flex-col items-end text-sm md:flex">
                        <div class="mb-1 flex items-center gap-1">
                            <span class="font-semibold text-gray-700 dark:text-gray-300">Category:</span>
                            <template v-for="(category, index) in article.categories" :key="category.id">
                                <a :href="route('category.show', category.slug)" class="transition-colors duration-200 hover:text-green-400">
                                    {{ category.name }}
                                </a>
                                <span v-if="index < article.categories.length - 1">,</span>
                            </template>
                        </div>
                        <div class="flex items-center gap-1">
                            <span class="font-semibold text-gray-700 dark:text-gray-300">Tags:</span>
                            <template v-for="(tag, index) in article.tags" :key="tag.id">
                                <a :href="`/tag/${tag.slug}`" class="transition-colors duration-200 hover:text-green-400">
                                    {{ tag.name }}
                                </a>
                                <span v-if="index < article.tags.length - 1">,</span>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="mb-8 flex flex-wrap items-center justify-between gap-6 border-b border-gray-200 pb-6 text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400"
            >
                <div class="flex items-center gap-6">
                    <div class="flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                            <path
                                fill-rule="evenodd"
                                d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                clip-rule="evenodd"
                            />
                        </svg>
                        <span class="font-medium">{{ article.views }} Views</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path
                                d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"
                            />
                        </svg>
                        <span class="font-medium">{{ article.hits }} Hits</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path
                                fill-rule="evenodd"
                                d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                                clip-rule="evenodd"
                            />
                        </svg>
                        <span class="font-medium">{{ article.likes_count }} Likes</span>
                    </div>
                </div>

                <div class="flex w-full items-center justify-between gap-5 md:w-auto lg:gap-2">
                    <LikeButton :post-id="article.id" :initial-liked="article.likedByUser" :initial-count="article.likes_count" />
                    <Button
                        v-tooltip.top="'Comment'"
                        rounded
                        icon="pi pi-comment"
                        severity="secondary"
                        size="large"
                        @click="$emit('scrollTo')"
                        class="p-button-text p-button-plain h-13 max-w-13 hover:!bg-gray-200 dark:hover:!bg-zinc-800"
                    />
                    <ShareModal :article="article" />
                    <Button
                        @click="report = true"
                        v-tooltip.top="'Report'"
                        rounded
                        icon="pi pi-flag"
                        severity="secondary"
                        size="large"
                        class="p-button-text p-button-plain h-13 max-w-13 hover:!bg-gray-200 dark:hover:!bg-zinc-800"
                    />

                    <Dialog v-model:visible="report" modal header="Laporkan Artikel" :style="{ width: '25rem' }" :draggable="false">
                        <div class="flex flex-col gap-4">
                            <label for="reason" class="text-sm font-semibold">Pilih Alasan</label>
                            <select v-model="reportReason" id="reason" class="w-full rounded border p-2">
                                <option value="" disabled>Pilih alasan</option>
                                <option value="spam">Spam / Iklan</option>
                                <option value="hate">Kebencian / Ujaran Kebencian</option>
                                <option value="violence">Kekerasan / Konten Tidak Pantas</option>
                                <option value="other">Lainnya</option>
                            </select>

                            <textarea
                                v-model="reportMessage"
                                rows="4"
                                placeholder="Tulis detail laporan (opsional)"
                                class="w-full rounded border p-2"
                            ></textarea>

                            <div class="flex justify-end gap-2">
                                <Button label="Batal" icon="pi pi-times" severity="secondary" @click="report = false" />
                                <Button label="Kirim Laporan" icon="pi pi-send" severity="danger" @click="submitReport" :disabled="!reportReason" />
                            </div>
                        </div>
                    </Dialog>

                    <Button
                        v-tooltip.top="'More'"
                        @click="toggle"
                        rounded
                        icon="pi pi-ellipsis-v"
                        severity="secondary"
                        size="large"
                        class="p-button-text p-button-plain h-13 max-w-13 hover:!bg-gray-200 dark:hover:!bg-zinc-800"
                    />
                    <Menu :base-z-index="-2000" ref="menu" id="overlay_menu" :model="menuitems" :popup="true" />
                </div>
            </div>
        </div>

        <div class="bg-white pt-10 pb-8 dark:bg-zinc-800">
            <div class="container mx-auto flex flex-col px-4 md:flex-row">
                <div class="w-full px-4 md:w-3/4">
                    <img :src="article.cover_url" alt="cover" class="mb-8 rounded-2xl" />
                    <article
                        class="format max-w-none text-gray-600 max-sm:text-sm lg:text-lg dark:text-gray-200 dark:format-invert [&_strong]:dark:text-gray-100"
                        v-html="article.body"
                    ></article>
                </div>
                <div class="w-full space-y-6 px-4 md:w-1/4">
                    <div class="sticky top-20 rounded-lg bg-white p-4 shadow dark:bg-zinc-800">
                        <h2 class="mb-4 text-xl font-bold text-gray-800 md:text-lg dark:text-gray-100">Recent Posts</h2>
                        <div v-for="article in recent" :key="article.id" class="mb-4">
                            <a :href="route('article.show', { id: article.id, slug: article.slug })" class="group block">
                                <img
                                    :src="article.cover_url"
                                    alt="cover"
                                    class="mb-2 aspect-16/9 w-full rounded-lg object-cover transition group-hover:opacity-80"
                                />
                                <h3 class="font-medium text-gray-700 group-hover:text-green-600 md:text-xs dark:text-gray-200">
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
