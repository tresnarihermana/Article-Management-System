<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import PageNav from '@/components/PageNav.vue';
import ThemeMode from '@/components/ThemeMode.vue';
const { articles, tags, categories, users, title } =usePage().props;
</script>

<template>
    <header>
        <PageNav />
    </header>
     <slot />
     <ThemeMode class="fixed bottom-10 right-5 lg:hidden bg-gray-300/40 hover:bg-gray-300 dark:hover:bg-gray-300/40 !rounded-full"></ThemeMode>
</template>
