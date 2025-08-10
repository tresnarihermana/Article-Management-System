<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import ArticleContent from '@/components/ArticleContent.vue';
import Button from 'primevue/button';
import { router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { can } from '@/lib/can';
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
    Swal.fire({

        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, approve it!'
    }).then((result) => {
        if (result.isConfirmed) {
            router.put(route('approve', id), {}, {
                onSuccess: () => {
                    alert('Article approved!');
                }
            });
        }
    })

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

    <Head title="Articles" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <ArticleContent :article="article" />
        <div class="fixed bottom-0 right-0 my-5 mr-5" v-if="can('articles.approve')">
            <Button severity="danger" icon="pi pi-eject" label="Reject Article" style="margin-right: 6px;" />
            <Button @click="approveArticle(article.id)" severity="success" icon="pi pi-check" label="Approve Article" />
        </div>
    </AppLayout>
</template>