<script setup lang="ts">
import VueApexCharts from "vue3-apexcharts";
import { ref, computed } from "vue";

const props = defineProps<{
  categories?: { name: string; total_views: number }[];
  articles?: { date: string; total_views: number; total_hits: number }[];
  mode?: "categories" | "views-vs-hits";
}>();

const series = ref<any[]>([]);
const chartOptions = ref<any>({
  chart: {
    type: "bar",
    toolbar: { show: false },
  },
  plotOptions: {
    bar: {
      borderRadius: 6,
      horizontal: false,
    },
  },
  dataLabels: { enabled: false },
  xaxis: { categories: [] },
  tooltip: { theme: "dark" },
});

// transform data
if (props.mode === "views-vs-hits" && props.articles) {
  chartOptions.value.xaxis.categories = props.articles.map((a) => a.date);
  series.value = [
    {
      name: "Views",
      data: props.articles.map((a) => a.total_views),
    },
    {
      name: "Hits",
      data: props.articles.map((a) => a.total_hits),
    },
  ];
} else if (props.categories) {
  chartOptions.value.xaxis.categories = props.categories.map((c) => c.name);
  series.value = [
    {
      name: "Views",
      data: props.categories.map((c) => c.total_views),
    },
  ];
}
</script>

<template>
  <VueApexCharts type="bar" height="100%" width="100%" :options="chartOptions" :series="series" />
</template>
