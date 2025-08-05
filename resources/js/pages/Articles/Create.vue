<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import { reactive, watch, ref, onMounted } from 'vue';
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import Multiselect from 'vue-multiselect';
import 'vue-multiselect/dist/vue-multiselect.min.css'
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Article',
        href: '/articles',
    },
    {
        title: 'Create Article',
        href: '/create',
    },
];
const props = defineProps({
    categories: Object,
    tags: Array,
})
const form = useForm({
    title: '',
    category_id: '',
    tag_ids: [],
    body: null,
})
const submit = () => {
    form.category_id = form.category_id.id
    form.tag_ids = form.tag_ids.map(tag => tag.id)
    form.post(route('articles.store'), {
        onError: (errors) => {
            console.log(errors);
        },
    });
};

// realtime validate password
const rules = reactive({
    minLength: false,
    hasUppercase: false,
    hasLowercase: false,
    hasNumber: false,
    hasSymbol: false,
    hasSpace: false
})
function validatePassword() {
    const val = form.password

    rules.minLength = val.length >= 8
    rules.hasUppercase = /[A-Z]/.test(val)
    rules.hasLowercase = /[a-z]/.test(val)
    rules.hasNumber = /[0-9]/.test(val)
    rules.hasSymbol = /[^A-Za-z0-9]/.test(val)
    rules.hasSpace = /\s/.test(val)
}
watch(() => form.password, validatePassword)
const tagNames = props.tags.map(tag => tag.name);
// ini buat cat(egori)
const cat = props.categories.map( 
    category => ({
    id: category.id,
    name: category.name,
}))
// ini buat tag
const tags = props.tags.map(
    tag => ({
        id: tag.id,
        name: tag.name,
    })
);

</script>

<template>

    <Head title="Article Create" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <!-- component -->
        <div class="text-gray-900 bg-gray-200">
            <div class="p-4 flex">
                <h1 class="text-3xl">
                    Create New Article
                </h1>
            </div>
        </div>
        <div class="relative">
        <input v-model="form.title" type="text" placeholder="Judul Artikel"
        class="border p-2 w-full focus:outline-none focus:ring-0" />
      </div>
      <div class="relative">
        <!-- <select v-model="form.category_id"
            class=" w-full bg-white text-gray-700 py-2 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
            <option :value="null">Select Categories</option>
            <option v-for="category in categories" :value="category.id" :key="category.id">{{ category.name }}</option>
        </select> -->
    
    <multiselect
    v-model="form.category_id" :options="cat" :multiple="false" 
                 :group-select="true" placeholder="Articles Category" track-by="name" :label="`name`" :searchable="false"><template v-slot:noResult>Oops! No elements found. Consider changing the search query.</template>
    </multiselect>
    <multiselect v-model="form.tag_ids" :options="tags" :multiple="true"
                 :group-select="true" placeholder="Type to search tags" track-by="name" :label="`name`"><template v-slot:noResult>Oops! No elements found. Consider changing the search query.</template>
    </multiselect>
                </div>
        <QuillEditor v-model:content="form.body" theme="snow" toolbar="full" contentType="delta" />
        <div class="px-4 py-3">
            <Button label="Back" as="a" :href="route('articles.index')" icon="pi pi-arrow-left" icon-pos="left" />
            <button @click="submit" class="bg-blue-600 text-white px-4 py-2 rounde absolute right-0">
                Simpan Artikel
            </button>
        </div>

    </AppLayout>
</template>
