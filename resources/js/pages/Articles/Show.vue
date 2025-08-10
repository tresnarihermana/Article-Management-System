<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import { ref, vModelCheckbox } from 'vue';
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

const form = useForm({
    status: page.props.article.status,
    rejected_message: '',
})

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
                    // alert('Article approved!');
                    Swal.fire({
                        title: 'Process Succeed',
                        text: 'Article Sudah Di Setujui',
                        icon: 'success',
                        timer: 1000,
                    })
                }
            });
        }
    })

};

const rejectArticle = (id: number) => {
    Swal.fire({
        title: 'Insert Reject Message',
        text: 'Masukkan alasan kenapa article ini ditolak!',
        icon: 'warning',
        input: 'text',
        inputAttributes: {
            autocapitalize: 'off',
            name: 'rejected_message',
            placeholder: 'Insert message here'
        },
        showCancelButton: true,
        cancelButtonText: 'Cancel',
        confirmButtonText: "Confirm!",
        confirmButtonColor: 'red',
        preConfirm(inputValue) {
            form.rejected_message = inputValue
        },
    }).then((result) => {
        if (result.isConfirmed) {
            form.status = 'rejected'
             form.put(route('reject', id), {
                onSuccess: () => {
                    // alert('Article rejected!');
                    Swal.fire({
                        title: 'Process Succeed!',
                        text: 'Article Sudah DI Reject',
                        icon: 'success',
                        timer: 1000,
                    })
                },
                onError: () => {
                     Swal.fire({
                        title: 'Process Fail!',
                        html: form.errors.rejected_message,
                        icon: 'error',
                        showConfirmButton: true,
                    })
                }
            });
        }
    })

};

const { article } = page.props;
</script>

<template>

    <Head title="Articles" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <ArticleContent :article="article" />
        <div class="fixed bottom-0 right-0 my-5 mr-5" v-if="can('articles.approve')">
            <Button @click="rejectArticle(article.id)" severity="danger" icon="pi pi-eject" label="Reject Article"
                style="margin-right: 6px;" />
            <Button @click="approveArticle(article.id)" severity="success" icon="pi pi-check" label="Approve Article" />
        </div>
    </AppLayout>
</template>