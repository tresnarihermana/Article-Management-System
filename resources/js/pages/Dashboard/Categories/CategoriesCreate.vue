<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import InputError from '@/components/InputError.vue';
import InputText from 'primevue/inputtext'
import Checkbox from 'primevue/checkbox';
import CheckboxGroup from 'primevue/checkboxgroup';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Categories',
        href: route('categories.index'),
    },
    {
        title: 'Category Create',
        href: '/categories',
    },
];

const form = useForm({
    name: '',
    description: '',
})

const submit = () => {
    form.post(route('categories.store'), {
        onError: (errors) => {
            console.log(errors);
        },
    });
};
</script>

<template>

    <Head title="categories Create" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <!-- component -->
        <div class="text-gray-900 bg-gray-200">
            <div class="p-4 flex">
                <h1 class="text-3xl">
                    Create New Category
                </h1>
            </div>

        </div>
        <div class="px-4 py-3">
            <Button label="Back" as="a" :href="route('categories.index')" icon="pi pi-arrow-left" icon-pos="left" />
        </div>
        <!-- component -->
        <div class="my-5">
            <!-- Main container for the form, responsive to screen sizes -->
            <div
                class="container mx-auto max-w-xs sm:max-w-md md:max-w-lg lg:max-w-xl shadow-md dark:shadow-white py-4 px-6 sm:px-10 bg-white dark:bg-gray-800 border-emerald-500 rounded-md">

                <div class="my-3">
                    <!-- Form title -->
                    <h1 class="text-center text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Create New
                        Category
                    </h1>
                    <form @submit.prevent="submit">

                        <!-- Input field for 'Name' -->
                        <div class="grid gap-2">
                            <Label for="name">Category name</Label>
                            <InputText id="name" type="text" required autofocus :tabindex="1" autocomplete="role_name"
                                v-model="form.name" placeholder="Enter Category Name" />
                            <InputError :message="form.errors.name" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="description">Category Description</Label>
                            <InputText id="description" type="text" required autofocus :tabindex="1"
                                autocomplete="role_name" v-model="form.description"
                                placeholder="Enter Category Description" />
                            <InputError :message="form.errors.description" />
                        </div>
                        <!-- Save button -->
                        <div class="py-2 ">
                            <Button label="Save" icon="pi pi-save" iconPos="right" @click="submit" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
