<script setup lang="ts">
import { ref } from 'vue';

const series = ref([10, 41, 35, 51, 49, 62, 69, 91, 148])

const chartOptions = ref({
  chart: {
    height: "100%",
    width: "100%",
    type: "pie",
  },
  labels: ['Category A', 'Category B', 'Category C', 'Category D', 'Category E'],
  responsive: [{
    breakpoint: 768,
    options: {
      chart: {
        width: 480
      },
      legend: {
        position: 'bottom'
      }
    }
  }]
})
</script>
<template>
  <div id="chart" class="relative aspect-video w-full overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
    <apexchart type="pie"  :options="chartOptions" :series="series" class="w-full h-full"></apexchart>
  </div>
</template>