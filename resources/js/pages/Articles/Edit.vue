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
import InputError from '@/components/InputError.vue';
const props = defineProps({
    article: Object,
    categories: Object,
    tags: Array,
})
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Article',
        href: route('articles.index'),
    },
    {
        title: 'Edit Article',
        href: route('articles.edit', props.article.id),
    },
];
const form = useForm({
    title: props.article?.title,
    category_id: props.article?.categories,
    tag_ids: props.article?.tags,
    cover: props.article.cover,
    body: props.article?.body,
    excerpt: props.article?.excerpt,
    status: '',
});
const submit = async() => {
      if (form.cover instanceof File) {
    await UploadArticleCover(); // Tunggu hingga upload selesai
  }
    form.category_id = form.category_id.id
    form.tag_ids = form.tag_ids.map(tag => tag.id)
    form.status = 'pending'
    form.put(route('articles.update', props.article.id), {
        onError: (errors) => {
            console.log(errors);
        },
    });
};
function saveAsDraft(){
  form.status = 'draft';
  form.put(route('articles.update', props.article.id), {
    preserveScroll: true,
  })
}
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

  const img = new Image()
  img.src = URL.createObjectURL(file)

  img.onload = () => {
    const targetRatio = 16 / 9
    const canvas = document.createElement('canvas')
    let sx, sy, sw, sh

    if (img.width / img.height > targetRatio) {
      sh = img.height
      sw = sh * targetRatio
      sx = (img.width - sw) / 2
      sy = 0
    } else {
      sw = img.width
      sh = sw / targetRatio
      sx = 0
      sy = (img.height - sh) / 2
    }

    canvas.width = 1280
    canvas.height = 720
    const ctx = canvas.getContext('2d')
    if (ctx) {
      ctx.drawImage(img, sx, sy, sw, sh, 0, 0, canvas.width, canvas.height)
      canvas.toBlob((blob) => {
        if (blob) {
          const croppedFile = new File([blob], file.name, { type: 'image/jpeg' })
          form.cover = croppedFile
          previewUrl.value = URL.createObjectURL(croppedFile)
        }
      }, 'image/jpeg', 0.9)
    }
  }
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
    <!-- Header -->
    <div class="text-gray-900 bg-gray-200">
      <div class="p-4 flex">
        <h1 class="text-3xl">Edit Article</h1>
      </div>
    </div>

    <!-- Form Edit Artikel -->
    <form @submit.prevent="submit" class="p-4 space-y-6">

      <!-- Judul Artikel -->
      <div>
        <label class="block mb-1 text-sm font-medium text-gray-700">Judul Artikel</label>
        <input
          v-model="form.title"
          type="text"
          placeholder="Masukkan judul artikel"
          class="border p-2 w-full rounded focus:outline-none focus:ring-2 focus:ring-blue-400"
        />
        <InputError :message="form.errors.title" />
      </div>

      <!-- Kategori Artikel -->
      <div>
        <label class="block mb-1 text-sm font-medium text-gray-700">Kategori</label>
        <multiselect
          v-model="form.category_id"
          :options="cat"
          :multiple="false"
          :group-select="true"
          placeholder="Pilih kategori artikel"
          track-by="name"
          :label="`name`"
          :searchable="false"
        >
          <template v-slot:noResult>Oops! Tidak ada kategori ditemukan.</template>
        </multiselect>
        <InputError :message="form.errors.category_id" />
      </div>

      <div>
        <label class="block mb-1 text-sm font-medium text-gray-700">Tags</label>
        <multiselect
          v-model="form.tag_ids"
          :options="tags"
          :multiple="true"
          :group-select="true"
          placeholder="Cari dan pilih tag"
          track-by="name"
          :label="`name`"
        >
          <template v-slot:noResult>Oops! Tidak ada tag ditemukan.</template>
        </multiselect>
        <InputError :message="form.errors.tag_ids" />
      </div>

      <!-- Konten Artikel -->
      <div>
        <label class="block mb-1 text-sm font-medium text-gray-700">Konten Artikel</label>
        <QuillEditor
          v-model:content="form.body"
          theme="snow"
          toolbar="full"
          contentType="html"
          style="height: 400px;"
        />
        <InputError :message="form.errors.body" />
      </div>
    <!-- Excerpt -->
    <div class="p-4">
      <label class="block mb-1 text-sm font-medium text-gray-700">Excerpt (Ringkasan)</label>
      <QuillEditor
        v-model:content="form.excerpt"
        theme="snow"
        toolbar="full"
        contentType="html"
        style="height: 200px;" />
      <InputError :message="form.errors.excerpt" />
    </div>
    </form>

    <!-- Upload Cover -->
    <form @submit.prevent="UploadArticleCover" class="p-4 pt-10">
      <label class="block mb-1 text-sm font-medium text-gray-700">Upload Gambar Cover</label>
      <div
        class="w-[400px] border-2 border-gray-300 border-dashed rounded-lg p-6"
        id="dropzone"
        @dragover.prevent="onDragOver"
        @drop.prevent="onDrop"
      >
        <input
          for="cover"
          ref="fileInput"
          type="file"
          accept="image/*"
          class="inset-0 w-full h-full opacity-0 z-50"
          @change="onFileChange"
        />
        <div class="text-center pointer-events-none">
          <img class="mx-auto h-12 w-12" src="https://www.svgrepo.com/show/357902/image-upload.svg" alt="upload" />
          <h3 class="mt-2 text-sm font-medium text-gray-900">
            <label for="file-upload" class="relative cursor-pointer">
              <span>Drag and drop</span>
              <span class="text-indigo-600"> atau telusuri </span>
              <span>untuk mengunggah</span>
              <input id="file-upload" name="file-upload" type="file" class="sr-only" />
            </label>
          </h3>
          <p class="mt-1 text-xs text-gray-500">PNG, JPG, GIF maksimal 10MB</p>
        </div>

        <!-- Preview Cover -->
        <img
          v-if="!previewUrl"
          :src="`/storage/${props.article?.cover}`"
          class="mt-4 mx-auto max-h-40"
          alt="Cover Saat Ini"
        />
        <img
          v-if="previewUrl"
          :src="previewUrl"
          class="mt-4 mx-auto max-h-40"
          alt="Preview Cover Baru"
        />
      </div>

            <!-- Tombol Simpan -->
      <div class="flex justify-end gap-3">
        <Button
          label="Kembali"
          as="a"
          :href="route('articles.index')"
          icon="pi pi-arrow-left"
          icon-pos="left"
          severity="secondary"
        />
        <Button
          type="submit"
          @click="saveAsDraft"
          label="Save as draft"
          severity="info"
          icon="pi pi-save"
          icon-pos="left"
        />
        <Button
          type="submit"
          @click="submit"
          label="Submit Article"
          severity="success"
          icon="pi pi-upload"
          icon-pos="left"
        />
      </div>
    </form>
  </AppLayout>
</template>

