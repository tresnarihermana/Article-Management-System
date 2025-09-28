<script setup lang="ts">
import { ref, onMounted, watch } from "vue";
import { Link } from "@inertiajs/vue3";
import { type NavItem } from "@/types";
import { ChevronDown, ChevronRight } from "lucide-vue-next";

const props = defineProps<{ 
  items: NavItem[], 
  collapsed?: boolean
}>();

const STORAGE_KEY = "sidebar-open-map";
const openMap = ref<Record<string, boolean>>({});

onMounted(() => {
  const saved = localStorage.getItem(STORAGE_KEY);
  if (saved) {
    openMap.value = JSON.parse(saved);
  }
});

watch(openMap, (val) => {
  localStorage.setItem(STORAGE_KEY, JSON.stringify(val));
}, { deep: true });

const toggle = (title: string) => {
  openMap.value[title] = !openMap.value[title];
};

const isOpen = (title: string) => !!openMap.value[title];
</script>

<template>
  <ul class="space-y-1">
    <li v-for="item in props.items" :key="item.title">
      <div v-if="item.children" class="flex flex-col">
        <button
          type="button"
          class="flex items-center justify-between gap-2 px-3 py-2 w-full rounded hover:bg-muted"
          @click="toggle(item.title)"
        >
          <div class="flex items-center gap-2">
            <component :is="item.icon" v-if="item.icon" class="w-5 h-5 text-neutral-600 hover:text-neutral-800 dark:text-neutral-300 dark:hover:text-neutral-100" />
            <span v-if="!props.collapsed" class="group-data-[collapsible=icon]:hidden">
              {{ item.title }}
            </span>
          </div>
          <template v-if="!props.collapsed">
            <ChevronDown v-if="isOpen(item.title)" class="w-4 h-4" />
            <ChevronRight v-else class="w-4 h-4" />
          </template>
        </button>
        <transition name="slide-fade">
          <ul
            v-if="isOpen(item.title) && !props.collapsed"
            class="ml-6 mt-1 space-y-1 border-l border-gray-700 pl-3"
          >
            <NavMain :items="item.children" :collapsed="props.collapsed" />
          </ul>
        </transition>
      </div>
      <Link
        v-else
        :href="item.href"
        :preserve-state="true"
        class="flex items-center gap-2 px-3 py-2 rounded hover:bg-muted"
      >
        <component :is="item.icon" v-if="item.icon" class="w-5 h-5 text-neutral-600 hover:text-neutral-800 dark:text-neutral-300 dark:hover:text-neutral-100" />
        <span v-if="!props.collapsed" class="group-data-[collapsible=icon]:hidden">
          {{ item.title }}
        </span>
      </Link>
    </li>
  </ul>
</template>
