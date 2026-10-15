<script setup>
import { computed } from "vue";
import { Chart as ChartJS, registerables } from "chart.js";
import { Bar, Line, Pie, Doughnut, Radar, PolarArea, Bubble, Scatter } from "vue-chartjs";

ChartJS.register(...registerables);

const props = defineProps({
  type: { type: String, default: "line" },
  chartdata: { type: Object, required: true },
  options: { type: Object, default: () => ({}) },
});

const components = { bar: Bar, line: Line, pie: Pie, doughnut: Doughnut, radar: Radar, polar: PolarArea, bubble: Bubble, scatter: Scatter };
const chartComponent = computed(() => components[props.type] ?? Line);
const colors = ["#1f77b4", "#aec7e8", "#ff7f0e", "#ffbb78", "#2ca02c", "#98df8a", "#d62728", "#ff9896", "#9467bd", "#c5b0d5", "#8c564b", "#c49c94", "#e377c2", "#f7b6d2", "#7f7f7f", "#c7c7c7", "#bcbd22", "#dbdb8d", "#17becf", "#9edae5"];

const chartData = computed(() => ({
  ...props.chartdata,
  datasets: props.chartdata.datasets.map((dataset, index) => {
    const color = ["pie", "doughnut", "polar"].includes(props.type)
      ? dataset.data.map((_, i) => colors[i % colors.length])
      : colors[index % colors.length];

    return { backgroundColor: color, borderColor: color, ...dataset };
  }),
}));

const chartOptions = computed(() => ({
  responsive: true,
  maintainAspectRatio: false,
  ...props.options,
  plugins: {
    ...props.options.plugins,
    legend: {
      display: false,
      position: "bottom",
      ...props.options.plugins?.legend,
    },
  },
}));
</script>

<template>
  <div style="height: 200px; position: relative">
    <component :is="chartComponent" :data="chartData" :options="chartOptions" />
  </div>
</template>
