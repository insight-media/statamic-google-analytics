import { h } from "vue";
import AnalyticsChart from "./components/AnalyticsChart.vue";
import PageStatsFieldtype from "./components/PageStatsFieldtype.vue";

Statamic.booting(() => {
  for (const type of ["bar", "line", "pie", "doughnut", "radar", "polar", "bubble", "scatter"]) {
    Statamic.$components.register(`${type}-chart`, {
      props: ["chartdata", "options"],
      render() {
        return h(AnalyticsChart, {
          type,
          chartdata: this.chartdata,
          options: this.options,
        });
      },
    });
  }

  Statamic.$components.register("ga_page_stats_field-fieldtype", PageStatsFieldtype);
});
