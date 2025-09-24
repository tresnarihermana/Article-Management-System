<script setup lang="ts">
import LikeButton from './LikeButton.vue'
import { ref, computed, onMounted, onUnmounted } from 'vue'
import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime'
import Swal from 'sweetalert2'
import { router } from "@inertiajs/vue3"
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css'

const props = defineProps({
    article: Object,
    comments: Array<any>,
    commentsPagination: Object
})

const comments = ref([...props.comments])
const comment = ref('')
const activeDropdown = ref<number | null>(null)

const submitComment = () => {
    if (!comment.value.trim()) return
    const content = comment.value
    router.post(route('comments.store', props.article.id), { content }, {
        preserveScroll: true,
        onSuccess: (page) => {
            if (page.props.comment) {
                comments.value.unshift(page.props.comment)
            } else {
                // fallback: buat object lokal sementara
                comments.value.unshift({
                    id: Date.now(),
                    content,
                    user: page.props.auth.user,
                    created_at: new Date().toISOString()
                })
            }
            comment.value = ''
        }
    })
}

const loadPage = (page: number) => {
    router.get(route('article.show', { id: props.article.id, slug: props.article.slug })
        , { page }, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: (pageProps) => {
            comments.value = pageProps.props.comments  // update komentar lokal
        }
    })
}

const visiblePages = computed(() => {
    const total = props.commentsPagination.last_page
    const current = props.commentsPagination.current_page
    let start = Math.max(current - 2, 1)
    let end = Math.min(start + 4, total)
    start = Math.max(end - 4, 1)
    const pages = []
    for (let i = start; i <= end; i++) pages.push(i)
    return pages
})

const toggleDropdown = (id: number) => {
    activeDropdown.value = activeDropdown.value === id ? null : id
}

const handleClickOutside = (e: MouseEvent) => {
    const target = e.target as HTMLElement
    if (!target.closest('.dropdown-btn') && !target.closest('.dropdown-menu')) {
        activeDropdown.value = null
    }
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside)
})
onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
})

dayjs.extend(relativeTime)
const formatDate = (date: string) => {
    return dayjs(date).fromNow()
}

const handleDeleteComment = (id: number) => {
    Swal.fire({
        title: 'Hapus komentar?',
        text: "Tindakan ini tidak bisa dibatalkan.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('comments.delete', id), {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    comments.value = comments.value.filter(c => c.id !== id)
                }
            })
        }
    })
}
</script>

<template>
    <section class="py-8 xl:py-16 2xl:pr-90 antialiased">
        <div class="max-w-6xl mx-auto relative px-4">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg lg:text-2xl font-bold text-gray-900 dark:text-white">
                    Discussion ({{ comments.length }})
                </h2>
                <LikeButton :post-id="article.id" :initial-liked="article.likedByUser"
                    :initial-count="article.likes_count" />
            </div>

            <form @submit.prevent="submitComment" class="mb-6">
                <QuillEditor v-model:content="comment" theme="snow"
                    :toolbar="['bold', 'italic', 'underline', 'link', 'strike', 'blockquote', 'code-block']"
                    contentType="html" style="height: 200px;" />
                <button type="submit" v-if="$page.props.auth.user"
                    class="inline-flex items-center py-3 mt-2 px-4 text-xs font-medium text-center text-white bg-green-700 rounded-lg focus:ring-4 focus:ring-green-200 dark:focus:ring-green-900 hover:bg-green-800">
                    Post comment
                </button>
            </form>

            <article v-for="c in comments" :key="c.id" class="p-6 text-base bg-white rounded-lg dark:bg-zinc-800 mb-4">
                <footer class="flex justify-between items-center mb-2">
                    <div class="flex items-center">
                        <p class="inline-flex items-center mr-3 text-sm text-gray-900 dark:text-white font-semibold">
                            <img class="mr-2 w-6 h-6 rounded-full"
                                :src="c.user.avatar_url ?? `https://ui-avatars.com/api/?name=${encodeURIComponent(c.user.name)}`"
                                alt="User avatar"
                                @error="$event.target.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(c.user.name || 'User')}`">
                            <a :href="route('profile.show', c.user.username)">{{ c.user.username }}</a>
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            <time :datetime="c.created_at">{{ new Date(c.created_at).toLocaleDateString() }} ({{
                                formatDate(c.created_at) }})</time>
                        </p>
                    </div>

                    <div class="relative">
                        <button @click.stop="toggleDropdown(c.id)"
                            class="dropdown-btn inline-flex items-center p-2 text-sm font-medium text-gray-500 dark:text-gray-400 bg-white rounded-lg hover:bg-gray-100 dark:bg-zinc-900 dark:hover:bg-zinc-700"
                            type="button">
                            <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                fill="currentColor" viewBox="0 0 16 3">
                                <path
                                    d="M2 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Zm6.041 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM14 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Z" />
                            </svg>
                        </button>
                        <div v-if="activeDropdown === c.id"
                            class="dropdown-menu absolute right-0 z-10 w-36 bg-white rounded divide-y divide-gray-100 shadow dark:bg-zinc-700 dark:divide-gray-600 mt-2">
                            <ul class="py-1 text-sm text-gray-700 dark:text-gray-200">
                                <li><a href="#" v-if="$page.props.auth.user.id === c.user.id"
                                        class="block py-2 px-4 hover:bg-gray-100 dark:hover:bg-zinc-600 dark:hover:text-white">Edit</a>
                                </li>
                                <li><a @click.prevent="handleDeleteComment(c.id)"
                                        v-if="$page.props.auth.user.id === c.user.id"
                                        class="block py-2 px-4 hover:bg-gray-100 dark:hover:bg-zinc-600 dark:hover:text-white">Remove</a>
                                </li>
                                <li><a href="#"
                                        class="block py-2 px-4 hover:bg-gray-100 dark:hover:bg-zinc-600 dark:hover:text-white">Report</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </footer>
                <p class="text-gray-500 dark:text-gray-400" v-html="c.content"></p>
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
