<template>
  <Button
  v-tooltip.top="liked ? 'Unlike' : 'Like'"
    :icon="liked ? 'pi pi-heart-fill' : 'pi pi-heart'"
    :label="likesCount"
    rounded
    size="large"
    class="p-button-text p-button-plain max-w-13 h-13 hover:!bg-gray-200 dark:hover:!bg-zinc-800"
    :style="{ color: liked ? 'red' : 'gray' }"
    @click="toggleLike"
  />
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'
import Button from 'primevue/button'

const props = defineProps({
  postId: Number,
  initialLiked: Boolean,
  initialCount: Number
})

const liked = ref(props.initialLiked)
const likesCount = ref(props.initialCount)

const toggleLike = async () => {
  try {
    const res = await axios.post(`/article/read/${props.postId}/like`)
    liked.value = res.data.liked
    likesCount.value = res.data.likes_count
  } catch (err) {
    console.error(err)
  }
}
</script>

