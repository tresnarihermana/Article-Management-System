<script setup lang="ts">
import { useInitials } from '@/composables/useInitials';
import { Head, router } from '@inertiajs/vue3';
import Button from 'primevue/button';
import LikeButton from './LikeButton.vue';
import { ref } from 'vue';
import ShareModal from './Main/ShareModal.vue';
import Menu from 'primevue/menu';
import Dialog from 'primevue/dialog';
import Swal from 'sweetalert2';
const props = defineProps({
    article: Object,
    categories: Array,
    recent: Array<any>,
})
const { getInitials } = useInitials();



const goToProfile = () => {
    router.get(route('profile.show', props.article.user.username))
}
const emit = defineEmits(['scrollTo'])

const menu = ref();
const menuitems = ref([
    {
        label: 'Options',
        items: [

            {
                label: 'Download as PDF',
                icon: 'pi pi-download',
                command: () => {
                    window.location.href = route('articles.export-pdf', props.article.id);
                }
            }
        ]
    }
]);

const toggle = (event) => {
    menu.value.toggle(event);
};
const report = ref(false);

const reportReason = ref('')
const reportMessage = ref('')

const submitReport = () => {
    if (!reportReason.value) return

    router.post(route('articles.report', props.article.id), {
        reason: reportReason.value,
        message: reportMessage.value,
    }, {
        onSuccess: () => {
            report.value = false
            reportReason.value = ''
            reportMessage.value = ''
            Swal.fire('Berhasil', 'Laporan sudah dikirim', 'success')
        }
    })
}

</script>

<template>

    <Head :title="article?.title">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div class="flex flex-col py-14">
        <div class="container mx-auto px-4 pt-12">
            <div class="flex flex-col gap-8 mb-8 border-b-2 pb-6 border-gray-200 dark:border-gray-700">
                <div class="max-w-2xl">
                    <h1
                        class="text-4xl md:text-5xl font-extrabold font-sans text-gray-900 dark:text-gray-100 leading-tight">
                        {{ article.title }}
                    </h1>
                </div>

                <div class="flex items-center justify-between text-gray-600 dark:text-gray-400">
                    <div class="flex items-center gap-4">
                        <img :src="article.user.avatar ? article.user.avatar_url : `https://ui-avatars.com/api/?name=${getInitials(article.user.username)}&background=random`"
                            @click="goToProfile" alt="user avatar"
                            class="w-12 h-12 rounded-full object-cover border-2 border-green-500 cursor-pointer" />
                        <div>
                            <a :href="route('profile.show', article.user.username)"
                                class="text-lg font-bold hover:text-green-500 transition-colors duration-200">
                                {{ article.user.name }}
                            </a>
                            <div class="text-sm text-gray-500 dark:text-gray-500">
                                <p>{{ article.published_at }} &bull; {{ article.read_time }} min read</p>
                            </div>
                        </div>
                    </div>

                    <div class="hidden md:flex flex-col items-end text-sm">
                        <div class="flex items-center gap-1 mb-1">
                            <span class="font-semibold text-gray-700 dark:text-gray-300">Category:</span>
                            <template v-for="(category, index) in article.categories" :key="category.id">
                                <a :href="route('category.show', category.slug)"
                                    class="hover:text-green-400 transition-colors duration-200">
                                    {{ category.name }}
                                </a>
                                <span v-if="index < article.categories.length - 1">,</span>
                            </template>
                        </div>
                        <div class="flex items-center gap-1">
                            <span class="font-semibold text-gray-700 dark:text-gray-300">Tags:</span>
                            <template v-for="(tag, index) in article.tags" :key="tag.id">
                                <a :href="`/tag/${tag.slug}`"
                                    class="hover:text-green-400 transition-colors duration-200">
                                    {{ tag.name }}
                                </a>
                                <span v-if="index < article.tags.length - 1">,</span>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="flex flex-wrap items-center justify-between gap-6 text-sm text-gray-500 dark:text-gray-400 mb-8 border-b border-gray-200 dark:border-gray-700 pb-6">
                <div class="flex items-center gap-6">
                    <div class="flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                            <path fill-rule="evenodd"
                                d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="font-medium">{{ article.views }} Views</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path
                                d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z" />
                        </svg>
                        <span class="font-medium">{{ article.hits }} Hits</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="font-medium">{{ article.likes_count }} Likes</span>
                    </div>
                </div>

                <div class="flex items-center gap-5 lg:gap-2 w-full md:w-auto justify-between">
                    <LikeButton :post-id="article.id" :initial-liked="article.likedByUser"
                        :initial-count="article.likes_count" />
                    <Button v-tooltip.top="'Comment'" rounded icon="pi pi-comment" severity="secondary" size="large"
                        @click="$emit('scrollTo')"
                        class="p-button-text p-button-plain max-w-13 h-13 hover:!bg-gray-200 dark:hover:!bg-zinc-800" />
                    <ShareModal :article="article" />
<Button 
    @click="report = true" 
    v-tooltip.top="'Report'" 
    rounded 
    icon="pi pi-flag" 
    severity="secondary" 
    size="large"
    class="p-button-text p-button-plain max-w-13 h-13 hover:!bg-gray-200 dark:hover:!bg-zinc-800" 
/>

<Dialog 
    v-model:visible="report" 
    modal 
    header="Laporkan Artikel" 
    :style="{ width: '25rem' }"
    :draggable="false"
>
    <div class="flex flex-col gap-4">
        <label for="reason" class="font-semibold text-sm">Pilih Alasan</label>
        <select v-model="reportReason" id="reason" class="border rounded p-2 w-full">
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
            class="border rounded p-2 w-full"
        ></textarea>

        <div class="flex justify-end gap-2">
            <Button 
                label="Batal" 
                icon="pi pi-times" 
                severity="secondary" 
                @click="report = false" 
            />
            <Button 
                label="Kirim Laporan" 
                icon="pi pi-send" 
                severity="danger" 
                @click="submitReport" 
                :disabled="!reportReason"
            />
        </div>
    </div>
</Dialog>

                    <Button v-tooltip.top="'More'" @click="toggle" rounded icon="pi pi-ellipsis-v" severity="secondary"
                        size="large"
                        class="p-button-text p-button-plain max-w-13 h-13 hover:!bg-gray-200 dark:hover:!bg-zinc-800" />
                    <Menu :base-z-index="-2000" ref="menu" id="overlay_menu" :model="menuitems" :popup="true" />
                </div>
            </div>
        </div>

        <div class="bg-white pb-8 dark:bg-zinc-800 pt-10">
            <div class="container mx-auto px-4 flex flex-col md:flex-row">
                <div class="w-full md:w-3/4 px-4">
                    <img :src="article.cover_url" alt="cover" class="mb-8 rounded-2xl ">
                    <article
                        class="text-gray-600 [&_strong]:dark:text-gray-100 format dark:format-invert max-w-none dark:text-gray-200 lg:text-lg max-sm:text-sm"
                        v-html="article.body">

                    </article>
                </div>
                <div class="w-full md:w-1/4 px-4 space-y-6">
                    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-4 sticky top-20">
                        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-4 md:text-lg">Recent Posts</h2>
                        <div v-for="article in recent" :key="article.id" class="mb-4">
                            <a :href="route('article.show', { id: article.id, slug: article.slug })
                                " class="block group">
                                <img :src="article.cover_url" alt="cover"
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