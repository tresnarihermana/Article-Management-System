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
import axios from 'axios';
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Article',
        href: '/articles',
    },
    {
        title: 'Edit Article',
        href: '/edit',
    },
];
const props = defineProps({
    article: Object,
    categories: Object,
    tags: Array,
})
const form = useForm({
    title: props.article?.title,
    category_id: props.article?.categories,
    tag_ids: props.article?.tags,
    cover: props.article.cover,
    body: props.article?.body ? JSON.parse(props.article.body) : { ops: [] },
});
const submit = async() => {
      if (form.cover instanceof File) {
    await UploadArticleCover(); // Tunggu hingga upload selesai
  }
    form.category_id = form.category_id.id
    form.tag_ids = form.tag_ids.map(tag => tag.id)
    form.put(route('articles.update', props.article.id), {
        onError: (errors) => {
            console.log(errors);
        },
    });
};
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

const fileInput = ref<HTMLInputElement | null>(null)
const previewUrl = ref<string | null>(null)

const onFileChange = (e: Event) => {
    const files = (e.target as HTMLInputElement).files
    if (files && files.length > 0) {
        processFile(files[0])
    }
}

const onDrop = (e: DragEvent) => {
    const files = e.dataTransfer?.files
    if (files && files.length > 0) {
        processFile(files[0])
    }
}

const onDragOver = () => {
    // Optional: styling efek saat dragover
}

const processFile = (file: File) => {
    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif']
    if (!allowedTypes.includes(file.type)) {
        alert('Only JPG, PNG, and GIF files are allowed.')
        return
    }

    if (file.size > 10 * 1024 * 1024) {
        alert('File size must be less than 10MB.')
        return
    }
    form.cover = file;
    // Buat preview
    previewUrl.value = URL.createObjectURL(file)

}

const UploadArticleCover = async () => {
  const data = new FormData();

  data.append('cover', form.cover);
  data.append('title', form.title);      
  data.append('body', form.body);       
  data.append('excerpt', form.excerpt ?? '');
  data.append('category_id', form.category_id ?? '');

  (form.tag_ids ?? []).forEach((tagId, index) => {
    data.append(`tag_ids[${index}]`, tagId);
  });

  try {
    const response = await axios.post(route('articles.uploadCover'), data, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });

    form.cover = response.data.path;
    previewUrl.value = `/storage/${response.data.path}`;
  } catch (error) {
    console.error(error.response?.data?.errors);
  }
};

</script>

<template>

    <Head title="Edit Article" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <!-- component -->
        <div class="text-gray-900 bg-gray-200">
            <div class="p-4 flex">
                <h1 class="text-3xl">
                    Edit Article
                </h1>
            </div>
        </div>
        <form @submit.prevent="submit">
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
            <multiselect v-model="form.category_id" :options="cat" :multiple="false" :group-select="true"
                placeholder="Articles Category" track-by="name" :label="`name`" :searchable="false"><template
                    v-slot:noResult>Oops! No elements found. Consider changing the search query.</template>
            </multiselect>
            <multiselect v-model="form.tag_ids" :options="tags" :multiple="true" :group-select="true"
                placeholder="Type to search tags" track-by="name" :label="`name`"><template v-slot:noResult>Oops! No
                    elements found. Consider changing the search query.</template>
            </multiselect>
        </div>
        <QuillEditor v-model:content="form.body" theme="snow" toolbar="full" contentType="delta"/>
    </form> 
        <!-- upload Cover disini -->
            <form @submit.prevent="UploadArticleCover">
 <div class="relative mt-5 mx-5">
    <span class="text-sm text-base text-gray-900">Upload Image Here</span>
    <div
      class="w-[400px] relative border-2 border-gray-300 border-dashed rounded-lg p-6"
      id="dropzone"
      @dragover.prevent="onDragOver"
      @drop.prevent="onDrop"
    >
      <input
        for="cover"
        ref="fileInput"
        type="file"
        accept="image/*"
        class="absolute inset-0 w-full h-full opacity-0 z-50"
        @change="onFileChange"
      />
      <div class="text-center pointer-events-none">
        <img class="mx-auto h-12 w-12" src="https://www.svgrepo.com/show/357902/image-upload.svg" alt="upload" />
        <h3 class="mt-2 text-sm font-medium text-gray-900">
          <label for="file-upload" class="relative cursor-pointer">
            <span>Drag and drop</span>
            <span class="text-indigo-600"> or browse </span>
            <span>to upload</span>
            <input id="file-upload" name="file-upload" type="file" class="sr-only" />
          </label>
        </h3>
        <p class="mt-1 text-xs text-gray-500">
          PNG, JPG, GIF up to 10MB
        </p>
      </div>

      <img
        v-if="!previewUrl"
        :src="`/storage/${props.article?.cover}`"
        class="mt-4 mx-auto max-h-40"
        alt="Preview"
      />
      <img
        v-if="previewUrl"
        :src="previewUrl"
        class="mt-4 mx-auto max-h-40"
        alt="Preview"
      />
    </div>
    <button type="button" @click="UploadArticleCover">Upload</button>
  </div>
</form>
        <div class="px-4 py-3">
            <Button label="Back" as="a" :href="route('articles.index')" icon="pi pi-arrow-left" icon-pos="left" />
            <button @click="submit" class="bg-blue-600 text-white px-4 py-2 rounde absolute right-0">
                Simpan Artikel
            </button>
        </div>

    </AppLayout>
</template>
