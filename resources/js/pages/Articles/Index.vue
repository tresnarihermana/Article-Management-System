<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { usePage } from '@inertiajs/vue3';
import { can } from '@/lib/can';
import { ref, watch } from 'vue';
import Popover from 'primevue/popover';
const page = usePage();
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Articles',
        href: '/articles',
    },
];
const props = defineProps<{
    articles: {
        data: any[],
        current_page: number,
        last_page: number,
        per_page: number,
        total: number,
        next_page_url: string | null,
        prev_page_url: string | null
    },
    tags: any[],
    categories: any[],
    filters: {
        data: any[],
    }
}>();
const flash = page.props?.flash?.message;
if (flash) {
    let timerInterval;
    Swal.fire({
        title: "Process Success",
        icon: "success",
        html: "article Succesfully added",
        timer: 1000,
        timerProgressBar: true,
        didOpen: () => {
            const timer = Swal.getPopup().querySelector("b");
            timerInterval = setInterval(() => {
                timer.textContent = `${Swal.getTimerLeft()}`;
            }, 100);
        },
        willClose: () => {
            clearInterval(timerInterval);
        }
    })
}

function deleteArticle(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('articles.destroy', id), {
                onSuccess: () => {
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'This article has been deleted.',
                        icon: 'success',
                        timer: 1000,
                        timerProgressBar: true,
                    });
                },
                onError: () => {
                    Swal.fire({
                        title: 'Failed!',
                        text: 'Something went wrong. The article was not deleted.',
                        icon: 'error',
                    });
                }
            });
        }
    });
}
// pagination atau apalah
const perPage = ref(new URLSearchParams(window.location.search).get('per_page') || 10)
watch(perPage, (value) => {
    router.get(route('articles.index'), { per_page: value, page: 1 }, { preserveState: true, replace: true })
})
const form = useForm({
    search: props.filters.search || '',
    tag: props.filters.tag || null,

})
watch(() => form.search, () => {
    form.get(route('articles.index'), {
        preserveScroll: true,
        preserveState: true,
    });
});
watch(() => form.tag, () => {
    form.get(route('articles.index'), { preserveState: true, replace: true })
})

// pop over


const members = ref([
    { name: 'Amy Elsner', image: 'amyelsner.png', email: 'amy@email.com', role: 'Owner' },
    { name: 'Bernardo Dominic', image: 'bernardodominic.png', email: 'bernardo@email.com', role: 'Editor' },
    { name: 'Ioni Bowcher', image: 'ionibowcher.png', email: 'ioni@email.com', role: 'Viewer' }
]);

const popoverRefs = ref({});

function showPopover(event, articleId) {
  if (popoverRefs.value[articleId]) {
    popoverRefs.value[articleId].show(event);
  }
}
</script>

<template>

    <Head title="Articles" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <!-- component -->
        <div class="text-gray-900 bg-gray-200">
            <!-- <div class="p-4 flex">
                <h1 class="text-3xl">
                    Articles
                </h1>
            </div>
            <div class="px-4">
                <Button v-if="can('articles.create')" label="Add article" as="a" :href="route('articles.create')"
                    icon="pi pi-plus" icon-pos="left" />
            </div> -->

            <div class="antialiased font-sans bg-gray-200">
                <div class="container mx-auto px-4 sm:px-8">
                    <div class="py-8">
                        <div>
                            <h2 class="text-2xl font-semibold leading-tight">Articles</h2>
                        </div>
                        <div class="my-2 flex sm:flex-row flex-col">
                            <div class="flex flex-row mb-1 sm:mb-0">
                                <div class="relative">
                                    <select v-model="perPage"
                                        class="appearance-none h-full rounded-l border block appearance-none w-full bg-white border-gray-400 text-gray-700 py-2 px-4 pr-8 leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                                        <option :value="5">5</option>
                                        <option :value="10">10</option>
                                        <option :value="20">20</option>
                                    </select>
                                    <div
                                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20">
                                            <path
                                                d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="relative">
                                    <select v-model="form.tag"
                                        class="appearance-none h-full rounded-r border-t sm:rounded-r-none sm:border-r-0 border-r border-b block appearance-none w-full bg-white border-gray-400 text-gray-700 py-2 px-4 pr-8 leading-tight focus:outline-none focus:border-l focus:border-r focus:bg-white focus:border-gray-500">
                                        <option :value="null">All</option>
                                        <option v-for="tag in tags" :key="tag.id" :value="tag.name">{{ tag.name }}
                                        </option>
                                    </select>
                                    <div
                                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20">
                                            <path
                                                d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="block relative">
                                <span class="h-full absolute inset-y-0 left-0 flex items-center pl-2">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4 fill-current text-gray-500">
                                        <path
                                            d="M10 4a6 6 0 100 12 6 6 0 000-12zm-8 6a8 8 0 1114.32 4.906l5.387 5.387a1 1 0 01-1.414 1.414l-5.387-5.387A8 8 0 012 10z">
                                        </path>
                                    </svg>
                                </span>
                                <input v-model="form.search" placeholder="Search"
                                    class="appearance-none rounded-r rounded-l sm:rounded-l-none border border-gray-400 border-b block pl-8 pr-6 py-2 w-full bg-white text-sm placeholder-gray-400 text-gray-700 focus:bg-white focus:placeholder-gray-600 focus:text-gray-700 focus:outline-none" />
                            </div>
                            <div class="flex justify-end px-3">
                                <button v-if="can('articles.create')" @click="router.get(route('articles.create'))"
                                    type="button"
                                    class="rounded border block appearance-none bg-green-400 border-green-400 text-white py-2 px-4 pr-8 leading-tight focus:outline-none focus:bg-green focus:border-green-500">
                                    + Add Article
                                </button>
                            </div>
                        </div>

                        <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto">
                            <div class="inline-block w-full overflow-x-auto shadow rounded-lg">
                                <table class="min-w-[1200px] leading-normal">

                                    <thead>
                                        <tr>
                                            <th
                                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                ID
                                            </th>
                                            <th
                                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                title
                                            </th>
                                            <th
                                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                category
                                            </th>
                                            <th
                                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                tag
                                            </th>
                                            <th
                                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                Author
                                            </th>
                                            <th
                                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                Created At
                                            </th>
                                            <th
                                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                Updated At
                                            </th>
                                            <th
                                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                Status
                                            </th>
                                            <th
                                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                Action
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="article in articles.data">
                                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 w-10 h-10">
                                                        {{ article.id }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                <p class="text-gray-900 whitespace-no-wrap">
                                                    {{ article.title }}
                                                </p>
                                            </td>
                                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                <p class="text-gray-900 whitespace-no-wrap"
                                                    v-for="category in article.categories" :key="category.id">
                                                    {{ category.name }}
                                                </p>
                                            </td>
                                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm max-w-s">
                                                <div class="flex whitespace-nowrap gap-1">
                                                    <template v-for="(tag, index) in article.tags.slice(0, 5)"
                                                        :key="tag.id">
                                                        <span
                                                            class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                                            {{ tag.name }}
                                                        </span>
                                                    </template>
                                                    <span v-if="article.tags.length > 5"
                                                        class="bg-gray-100 text-gray-600 text-xs font-medium px-2.5 py-0.5 rounded"
                                                        :title="article.tags.map(t => t.name).join(', ')">
                                                        +{{ article.tags.length - 5 }} more
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                <p class="text-gray-900 whitespace-no-wrap">
                                                    {{ article.user.name }}
                                                </p>
                                            </td>
                                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                <p class="text-gray-900 whitespace-no-wrap">
                                                    {{ article.created_at }}
                                                </p>
                                            </td>
                                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                <p class="text-gray-900 whitespace-no-wrap">
                                                    {{ article.updated_at }}
                                                </p>
                                            </td>
                                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                <span @mouseover="showPopover($event, article.id)"
                                                    class="cursor-pointer relative inline-block px-3 py-1 font-semibold leading-tight"
                                                    :class="{
                                                        'text-green-900': article.status === 'published',
                                                        'text-blue-900': article.status === 'draft',
                                                        'text-gray-900': article.status === 'pending',
                                                        'text-orange-500': article.status === 'rejected',
                                                    }">
                                                    <span aria-hidden class="absolute inset-0 opacity-50 rounded-full"
                                                        :class="{
                                                            'bg-green-200': article.status === 'published',
                                                            'bg-blue-200': article.status === 'draft',
                                                            'bg-gray-300': article.status === 'pending',
                                                            'bg-orange-200': article.status === 'rejected'
                                                        }"></span>


                                                     <Popover :ref="el => popoverRefs[article.id] = el">
                                                        <div class="flex flex-col gap-4 w-[25rem]">
                                                            <div>
                                                                <span class="font-medium block mb-2">Team Members</span>
                                                                <ul class="list-none p-0 m-0 flex flex-col gap-4">
                                                                    <li v-for="member in members" :key="member.name"
                                                                        class="flex items-center gap-2">
                                                                        <img :src="`https://primefaces.org/cdn/primevue/images/avatar/${member.image}`"
                                                                            style="width: 32px" />
                                                                        <div>
                                                                            <span class="font-medium">{{ member.name
                                                                                }}</span>
                                                                            <div
                                                                                class="text-sm text-surface-500 dark:text-surface-400">
                                                                                {{ member.email }}</div>
                                                                        </div>
                                                                        <div
                                                                            class="flex items-center gap-2 text-surface-500 dark:text-surface-400 ml-auto text-sm">
                                                                            <span>{{ member.role }}</span>
                                                                            <i class="pi pi-angle-down"></i>
                                                                        </div>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </Popover> <span class="relative">{{ article.status }}</span>

                                                </span>
                                            </td>
                                            <td
                                                class="px-5 py-5 border-b border-gray-200 bg-white text-sm whitespace-nowrap">
                                                <Link v-if="can('articles.show')"
                                                    :href="route('articles.show', article.slug)" type="button"
                                                    class="mr-3 text-sm bg-green-500 hover:bg-green-700 text-white py-1 px-2 rounded focus:outline-none focus:shadow-outline">
                                                Show</Link>
                                                <Link v-if="can('articles.edit')"
                                                    :href="route('articles.edit', article.id)" type="button"
                                                    class="mr-3 text-sm bg-blue-500 hover:bg-blue-700 text-white py-1 px-2 rounded focus:outline-none focus:shadow-outline">
                                                Edit</Link>
                                                <button v-if="can('articles.delete')" @click="deleteArticle(article.id)"
                                                    type="button"
                                                    class="text-sm bg-red-500 hover:bg-red-700 text-white py-1 px-2 rounded focus:outline-none focus:shadow-outline">Delete</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div
                                    class="px-5 py-5 bg-white border-t flex flex-col xs:flex-row items-center xs:justify-between          ">
                                    <span class="text-xs xs:text-sm text-gray-900">
                                        Showing
                                        {{ (articles.current_page - 1) * articles.per_page + 1 }}
                                        to
                                        {{
                                            articles.current_page * articles.per_page > articles.total
                                                ? articles.total
                                                : articles.current_page * articles.per_page
                                        }}
                                        of {{ articles.total }} entries
                                    </span>
                                    <div class="inline-flex mt-2 xs:mt-0 flex">
                                        <button @click="router.get(articles.prev_page_url)"
                                            :disabled="!articles.prev_page_url"
                                            class="text-sm bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-l">
                                            Prev
                                        </button>

                                        <button @click="router.get(articles.next_page_url)"
                                            :disabled="!articles.next_page_url"
                                            class="text-sm bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-r">
                                            Next
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </AppLayout>
</template>
