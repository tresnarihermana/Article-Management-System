<script setup lang="ts">
import { ref, watch } from 'vue';

const props = defineProps<{
  articles: { date: string, total_views: number, total_hits: number }[]
}>();

const series = ref([
  {
    name: 'Views',
    data: props.articles.map(a => a.total_views)
  },
  {
    name: 'Hits',
    data: props.articles.map(a => a.total_hits)
  }
]);

const chartOptions = ref({
  chart: { type: 'area', height: 350 },
  dataLabels: { enabled: false },
  stroke: { curve: 'smooth' },
  xaxis: { type: 'datetime', categories: props.articles.map(a => a.date) },
  tooltip: { x: { format: 'dd/MM/yyyy' } },
  legend: { position: 'top' }
});

watch(() => props.articles, (newVal) => {
  series.value = [
    { name: 'Views', data: newVal.map(a => a.total_views) },
    { name: 'Hits', data: newVal.map(a => a.total_hits) }
  ];
  chartOptions.value.xaxis.categories = newVal.map(a => a.date);
});
</script>

<template>
  <div id="chart">
    <apexchart type="area" width="100%" :series="series" :options="chartOptions" />
  </div>
</template>
