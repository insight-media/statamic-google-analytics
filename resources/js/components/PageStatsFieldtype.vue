<script>
import { FieldtypeMixin } from "@statamic/cms";
import AnalyticsChart from "./AnalyticsChart.vue";

export default {
  mixins: [FieldtypeMixin],
  components: { AnalyticsChart },
  data() {
    return {
      chartdata: null,
      options: { plugins: { legend: { display: true } } },
    };
  },
  watch: {
    "value.entry": {
      immediate: true,
      async handler(entry) {
        this.chartdata = null;

        if (!entry) return;

        try {
          const { data } = await this.$axios.get(this.value.url, { params: { entry } });

          if (entry === this.value.entry && data.datasets) {
            this.chartdata = data;
          }
        } catch (error) {
          console.error(error);
        }
      },
    },
  },
};
</script>

<template>
  <AnalyticsChart v-if="chartdata" :chartdata="chartdata" :options="options" />
</template>
