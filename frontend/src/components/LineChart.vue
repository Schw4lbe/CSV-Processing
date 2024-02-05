<template>
  <div class="chart-container">
    <pie-chart :data="chartData" />
  </div>
</template>

<script>
import { mapGetters } from "vuex";

export default {
  name: "LineChart",

  computed: {
    ...mapGetters(["getChartData"]),
  },

  watch: {
    getChartData(newVal, oldVal) {
      if (newVal !== oldVal) {
        console.log("data change: ", newVal);
      }
    },
  },

  data() {
    return {
      chartData: [
        ["Blueberry", 44],
        ["Strawberry", 23],
      ],
    };
  },
};
</script>

<style scoped>
.chart-container {
  margin: 2rem;
}
</style>
