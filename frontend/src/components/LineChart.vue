<template>
  <div class="chart-container">
    <pie-chart :data="chartData" />
  </div>
</template>

<script>
export default {
  name: "LineChart",

  data() {
    return {
      chartData: [
        ["Blueberry", 44],
        ["Strawberry", 23],
      ],
    };
  },
};

// possebilities: Herren, Damen, Kinder, Babies, NONE
// possebiliets dynamic out of content in cells
// filter by Geschlecht
</script>

<style scoped>
.chart-container {
  margin: 2rem;
}
</style>
