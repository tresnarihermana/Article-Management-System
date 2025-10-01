<script setup lang="ts">
const props = defineProps({
    popArticles: Array,
})
</script>
<template>
    <div class="w-full lg:w-1/3 lg:pl-8 lg:pt-14 lg:pb-8 order-first lg:order-last pt-10">
        <div class="w-full bg-white dark:bg-zinc-800 sticky top-20 rounded-2xl shadow-md overflow-hidden">
            <div class="p-4 bg-gray-100 dark:bg-zinc-900 border-b dark:border-gray-700">
                <h2 class="text-lg font-bold dark:text-green-500">Most Popular</h2>
            </div>
            <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                <li v-for="pop in popArticles.slice(0, 5)" :key="pop.id">
                    <a class="block px-6 py-4 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition"
                        :href="route('article.show', { id: pop.id, slug: pop.slug })">
                        {{ pop.title.length > 40 ? pop.title.slice(0, 40) + '...' : pop.title }}
                    </a>
                </li>
            </ul>
        </div>
    </div>
</template>