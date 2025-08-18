<template>
<div class="-mx-3 block rounded-lg px-3 py-2 text-base/7 font-semibold text-gray-900  dark:text-gray-200 ">
  <form @submit.prevent="doSearch" class="relative flex items-center">
    <input
      type="text"
      v-model="search"
      class="w-full bg-gray-100 dark:bg-zinc-800/40 h-10 px-5 pr-10 rounded-full text-sm focus:outline-none transition-all duration-300 ease-in-out"
      placeholder="Search..."
    >
    <button
      type="submit"
      class="absolute right-2 top-1/2 -translate-y-1/2"
    >
      <svg
        class="h-4 w-4 fill-current text-gray-600 dark:text-gray-200"
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 20 20"
      >
        <path
          d="M12.9 14.32a8 8 0 1 1 1.41-1.41l5.35 5.33-1.42 1.42-5.33-5.34zM8 14A6 6 0 1 0 8 2a6 6 0 0 0 0 12z"
        />
      </svg>
    </button>
  </form>
</div>
</template>

<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";

const search = ref("");

const doSearch = () => {
  if (search.value.trim() !== "") {
    router.get("/search", { search: search.value }, { preserveState: false, replace: true });
  }
};
</script>
