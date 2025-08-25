<script setup lang="ts">
import LikeButton from './LikeButton.vue'
import { ref, computed } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime'
const props = defineProps({
    article: Object,
    comments: Array<any>,
    commentsPagination: Object
})

const comment = ref('')

const submitComment = () => {
    if (!comment.value.trim()) return

    Inertia.post(route('comments.store', props.article.id), { content: comment.value }, {
        onSuccess: () => {
            comment.value = ''
        }
    })
}

const loadPage = (page: number) => {
    Inertia.get(route('article.show', props.article.slug), { page }, {
        preserveState: true,
        preserveScroll: true
    })
}

const visiblePages = computed(() => {
    const total = props.commentsPagination.last_page
    const current = props.commentsPagination.current_page
    let start = Math.max(current - 2, 1)
    let end = Math.min(start + 4, total)
    start = Math.max(end - 4, 1) // pastikan selalu 5 maksimal
    const pages = []
    for (let i = start; i <= end; i++) pages.push(i)
    return pages
})
const activeDropdown = ref(null)

const toggleDropdown = (id) => {
    if (activeDropdown.value === id) {
        activeDropdown.value = null
    } else {
        activeDropdown.value = id
    }


}
dayjs.extend(relativeTime)
const formatDate = (date) => {
  return dayjs(date).fromNow()
}
</script>

<template>
    <section class="py-8 lg:py-16 lg:pr-90 antialiased">
        <div class="max-w-6xl mx-auto relative px-4">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg lg:text-2xl font-bold text-gray-900 dark:text-white">
                    Discussion ({{ commentsPagination.total }})
                </h2>
                <LikeButton :post-id="article.id" :initial-liked="article.likedByUser"
                    :initial-count="article.likes_count" />
            </div>

            <form @submit.prevent="submitComment" class="mb-6">
                <div
                    class="py-2 px-5 mb-4 bg-white rounded-lg rounded-t-lg border border-gray-200 dark:bg-zinc-800 dark:border-gray-700">
                    <label for="comment" class="sr-only">Your comment</label>
                    <textarea id="comment" v-model="comment" rows="6"
                        class="px-0 w-full text-sm text-gray-900 border-0 focus:ring-0 focus:outline-none dark:text-white dark:placeholder-gray-400 dark:bg-zinc-800"
                        placeholder="Write a comment..." required></textarea>
                </div>
                <button type="submit"
                    class="inline-flex items-center py-2.5 px-4 text-xs font-medium text-center text-white bg-green-700 rounded-lg focus:ring-4 focus:ring-green-200 dark:focus:ring-green-900 hover:bg-green-800">
                    Post comment
                </button>
            </form>

            <article v-for="c in comments" :key="c.id" class="p-6 text-base bg-white rounded-lg dark:bg-zinc-800 mb-4">
                <footer class="flex justify-between items-center mb-2">
                    <div class="flex items-center">
                        <p class="inline-flex items-center mr-3 text-sm text-gray-900 dark:text-white font-semibold">
                            <img class="mr-2 w-6 h-6 rounded-full"
                                :src="c.user.avatar ? `/storage/${c.user.avatar}` : `https://ui-avatars.com/api/?name=${c.user.name}`"
                                alt="User avatar">
                            <a :href="route('profile.show', c.user.username)">{{ c.user.username }}</a>
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            <time :datetime="c.created_at">{{ new Date(c.created_at).toLocaleDateString() }}  ({{ formatDate(c.created_at) }})</time>
                        </p>
                    </div>

                    <!-- Dropdown Button -->
                    <div class="relative">
                        <button @click="toggleDropdown(c.id)"
                            class="inline-flex items-center p-2 text-sm font-medium text-gray-500 dark:text-gray-400 bg-white rounded-lg hover:bg-gray-100 dark:bg-zinc-900 dark:hover:bg-zinc-700"
                            type="button">
                            <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                fill="currentColor" viewBox="0 0 16 3">
                                <path
                                    d="M2 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Zm6.041 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM14 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Z" />
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div v-if="activeDropdown === c.id"
                            class="absolute right-0 z-10 w-36 bg-white rounded divide-y divide-gray-100 shadow dark:bg-zinc-700 dark:divide-gray-600 mt-2">
                            <ul class="py-1 text-sm text-gray-700 dark:text-gray-200">
                                <li><a href="#"
                                        class="block py-2 px-4 hover:bg-gray-100 dark:hover:bg-zinc-600 dark:hover:text-white">Edit</a>
                                </li>
                                <li><a href="#"
                                        class="block py-2 px-4 hover:bg-gray-100 dark:hover:bg-zinc-600 dark:hover:text-white">Remove</a>
                                </li>
                                <li><a href="#"
                                        class="block py-2 px-4 hover:bg-gray-100 dark:hover:bg-zinc-600 dark:hover:text-white">Report</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </footer>

                <p class="text-gray-500 dark:text-gray-400">{{ c.content }}</p>
            </article>

            <nav aria-label="Page navigation example">
                <ul class="flex items-center -space-x-px h-10 text-base">
                    <li>
                        <button @click="loadPage(commentsPagination.current_page - 1)"
                            :disabled="commentsPagination.current_page === 1"
                            class="flex items-center justify-center px-4 h-10 ms-0 leading-tight text-gray-500 bg-white border border-e-0 border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-400 dark:hover:bg-zinc-700 dark:hover:text-white">
                            <span class="sr-only">Previous</span>
                            <svg class="w-3 h-3 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M5 1 1 5l4 4" />
                            </svg>
                        </button>
                    </li>

                    <li v-for="page in visiblePages" :key="page">
                        <button @click="loadPage(page)"
                            :class="page === commentsPagination.current_page
                                ? 'z-10 flex items-center justify-center px-4 h-10 leading-tight text-blue-600 border border-blue-300 bg-blue-50 dark:border-zinc-700 dark:bg-zinc-700 dark:text-white'
                                : 'flex items-center justify-center px-4 h-10 leading-tight text-zinc-500 bg-white border border-zinc-300 hover:bg-zinc-100 hover:text-zinc-700 dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-400 dark:hover:bg-zinc-700 dark:hover:text-white'">
                            {{ page }}
                        </button>
                    </li>

                    <li>
                        <button @click="loadPage(commentsPagination.current_page + 1)"
                            :disabled="commentsPagination.current_page === commentsPagination.last_page"
                            class="flex items-center justify-center px-4 h-10 leading-tight text-zinc-500 bg-white border border-zinc-300 rounded-e-lg hover:bg-zinc-100 hover:text-zinc-700 dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-400 dark:hover:bg-zinc-700 dark:hover:text-white">
                            <span class="sr-only">Next</span>
                            <svg class="w-3 h-3 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 9 4-4-4-4" />
                            </svg>
                        </button>
                    </li>
                </ul>
            </nav>
        </div>
    </section>
</template>
