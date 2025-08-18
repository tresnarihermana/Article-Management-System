<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";

const search = ref("");
const expanded = ref(false);
const inputRef = ref(null);

const toggleSearch = () => {
  expanded.value = !expanded.value;
  if (expanded.value) {
    // Fokuskan ke input saat expand
    setTimeout(() => inputRef.value?.focus(), 50);
  }
};

const collapse = () => {
  if (search.value === "") {
    expanded.value = false;
  }
};

const doSearch = () => {
  if (search.value.trim() !== "") {
    router.get(
      "/search",
      { search: search.value },
      { preserveState: false, replace: true }
    );
  }
};
</script>

<template>
  <div class="relative flex items-center right-2">
    <form @submit.prevent="doSearch" class="flex items-center">
      <input
        ref="inputRef"
        type="text"
        v-model="search"
        :class="[
          'bg-white/10 dark:bg-zinc-700/40 h-10 px-5 pr-10 rounded-full text-sm focus:outline-none transition-all duration-300 ease-in-out',
          expanded ? 'w-64 xl:w-52 md:w-32' : 'w-12 cursor-pointer'
        ]"
        placeholder="Search..."
        @focus="expanded = true"
        @blur="collapse"
      >
      <button
        type="button"
        @click="toggleSearch"
        class="absolute right-0 top-0 mt-3 mr-4"
      >
        <svg
          class="h-4 w-4 fill-current"
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
