<script setup lang="ts">
import { usePage } from "@inertiajs/vue3";

const page = usePage();
const props = page.props as {
  topArticles: {
    title: string;
    categories: string[];
    views: number;
    percentage: number;
  }[];
};

const colors = [
  { bar: "bg-orange-500", text: "text-orange-500" },
  { bar: "bg-cyan-500", text: "text-cyan-500" },
  { bar: "bg-pink-500", text: "text-pink-500" },
  { bar: "bg-green-500", text: "text-green-500" },
  { bar: "bg-purple-500", text: "text-purple-500" },
  { bar: "bg-teal-500", text: "text-teal-500" },
];
</script>

<template>
  <div class="card">
    <div class="flex justify-between items-center mb-6">
      <div class="font-semibold text-xl">Top Articles This Week</div>
      <button
        class="p-button p-component p-button-icon-only p-button-text p-button-plain p-button-rounded"
        type="button"
      >
        <span class="p-button-icon pi pi-ellipsis-v"></span>
      </button>
    </div>

    <ul class="list-none p-0 m-0">
      <li
        v-for="(article, i) in props.topArticles"
        :key="i"
        class="flex flex-col md:flex-row md:items-center md:justify-between mb-6"
      >
        <div>
          <span class="text-surface-900 dark:text-surface-0 font-medium mr-2 mb-1 md:mb-0">
            {{ article.title }}
          </span>
          <div class="mt-1 text-muted-color">{{ article.category }}</div>
        </div>
        <div class="mt-2 md:mt-0 flex items-center">
          <div
            class="bg-surface-300 dark:bg-surface-500 rounded-border overflow-hidden w-40 lg:w-24"
            style="height:8px;"
          >
            <div
              class="h-full"
              :class="colors[i % colors.length].bar"
              :style="{ width: article.percentage + '%' }"
            ></div>
          </div>
          <span :class="[colors[i % colors.length].text, 'ml-4 font-medium']">
            {{ article.views.toLocaleString() }} Views
          </span>
        </div>
      </li>
    </ul>
  </div>
</template>
