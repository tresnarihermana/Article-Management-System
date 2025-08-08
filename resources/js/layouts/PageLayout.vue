<script setup lang="ts">
import PageLayout from '../layouts/Main/PageSimpleLayout.vue'

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <PageLayout>
        <slot />
    </PageLayout>
</template>
