<template>
  <div class="chart-container">
    <div class="chart-select-container">
      <div class="select-container">
        <div class="select">
          <label for="select1">Pie Chart</label>
          <select name="select1" id="select1">
            <option value="">test1</option>
            <option value="">test2</option>
            <option value="">test3</option>
            <option value="">test4</option>
          </select>
        </div>
        <div class="select">
          <label for="select2">Col Chart</label>
          <select name="select2" id="select2">
            <option value="">test1</option>
            <option value="">test2</option>
            <option value="">test3</option>
            <option value="">test4</option>
          </select>
        </div>
      </div>
      <pie-chart :data="pieChartData" />
    </div>
    <column-chart :data="columnChartData"></column-chart>
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
        this.setPieChartData(newVal);
        this.setColumnChartData(newVal);
      }
    },
  },

  data() {
    return {
      pieChartData: [],
      columnChartData: [],
    };
  },

  methods: {
    setPieChartData(data) {
      const counts = data.reduce((acc, el) => {
        acc[el.Geschlecht] = (acc[el.Geschlecht] || 0) + 1;
        return acc;
      }, {});
      Object.keys(counts).forEach((key) => {
        if (key === "") {
          counts["k.A."] = counts[key]; // Assign the value of the empty key to the new key 'k.A.'
          delete counts[key]; // Delete the old key
        }
      });
      this.pieChartData = Object.entries(counts);
    },

    setColumnChartData(data) {
      const counts = data.reduce((acc, el) => {
        acc[el.Grammatur] = (acc[el.Grammatur] || 0) + 1;
        return acc;
      }, {});
      Object.keys(counts).forEach((key) => {
        if (key === "") {
          counts["k.A."] = counts[key]; // Assign the value of the empty key to the new key 'k.A.'
          delete counts[key]; // Delete the old key
        }
      });
      this.columnChartData = Object.entries(counts);
    },
  },
};
</script>

<style>
.chart-container {
  width: 100%;
  padding: 1rem;
  padding-bottom: 2rem;
  display: grid;
  grid-template-columns: 1fr 2fr;
  background: #efefef;
}
.chart-select-container {
  display: flex;
}

.select-container {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  text-align: center;
}

label {
  color: black;
  margin-top: 2rem;
}

#select1,
#select2 {
  padding: 1rem;
  margin: 0 2rem 2rem 2rem;
  color: black;
  border: 1px solid black;
}
</style>
