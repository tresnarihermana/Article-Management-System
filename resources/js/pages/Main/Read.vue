<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import ArticleContent from '@/components/ArticleContent.vue';
import Button from 'primevue/button';
import { router } from '@inertiajs/vue3';
import PageLayout from '@/layouts/PageLayout.vue';
const page = usePage();
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Articles',
        href: '/articles',
    },
    {
        title: 'Show Article',
        href: '/show',
    },
];


const approveArticle = (id: number) => {
    router.put(route('approve', id), {}, {
        onSuccess: () => {
            alert('Article approved!');
        }
    });
};

const rejectArticle = (id: number) => {
    router.put(`/admin/articles/${id}/reject`, {}, {
        onSuccess: () => {
            alert('Article rejected!');
        }
    });
};

const { article } = page.props;
</script>

<template>

    <Head title="Article" />
    <PageLayout>
        <ArticleContent :article="article" />
    </PageLayout>
</template>