<template>
  <div v-if="quillLoaded">
    <QuillEditor
      v-model:content="content"
      theme="snow"
      toolbar="full"
      contentType="html"
      :modules="modules"
      :style="style"
      @update:content="updateContent"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, defineEmits, defineProps } from 'vue';

const props = defineProps(['modelValue', 'modules', 'style']);
const emit = defineEmits(['update:modelValue']);

const quillLoaded = ref(false);
const content = ref(props.modelValue);

onMounted(async () => {
  const { QuillEditor } = await import('@vueup/vue-quill');
  const BlotFormatter = (await import('quill-blot-formatter')).default;

  quillLoaded.value = true;
});

const updateContent = (newContent) => {
  emit('update:modelValue', newContent);
};
</script>