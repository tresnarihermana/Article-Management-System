<script setup lang="ts">
import PageNav from '@/components/PageNav.vue';
import ThemeMode from '@/components/ThemeMode.vue';

</script>

<template>
    <header>
        <PageNav />
    </header>
    <main>
     <slot />
     </main>
     <ThemeMode class="fixed bottom-10 right-5 lg:hidden bg-gray-300/40 hover:bg-gray-300 dark:hover:bg-gray-300/40 !rounded-full"></ThemeMode>
</template>
