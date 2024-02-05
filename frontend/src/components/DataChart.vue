<template>
  <div v-if="getTableName" class="chart-container">
    <div class="chart-select-container">
      <div class="select-container">
        <div class="select">
          <label for="select1">Select Pie Chart</label>
          <select
            @change="updateSelect"
            v-model="selectedChartCat1"
            name="select1"
            id="select1"
          >
            <option
              v-for="(item, index) in chartCategories"
              :key="index"
              :value="item"
            >
              {{ item }}
            </option>
          </select>
        </div>
        <div class="select">
          <label for="select2">Select Col Chart</label>
          <select
            @change="updateSelect"
            v-model="selectedChartCat2"
            name="select2"
            id="select2"
          >
            <option
              v-for="(item, index) in chartCategories"
              :key="index"
              :value="item"
            >
              {{ item }}
            </option>
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

  data() {
    return {
      chartCategories: [],
      pieChartData: [],
      columnChartData: [],

      // default values for pre select charts
      // will be overwritten uppon select something different
      selectedChartCat1: "Geschlecht",
      selectedChartCat2: "Grammatur",
    };
  },

  computed: {
    ...mapGetters(["getChartData", "getTableName"]),
  },

  watch: {
    getChartData(newVal, oldVal) {
      if (newVal !== oldVal) {
        this.setChartCategories(newVal);
        this.setChartData(newVal, this.selectedChartCat1, "select1");
        this.setChartData(newVal, this.selectedChartCat2, "select2");
        // this.setColumnChartData(newVal);
      }
    },
  },

  methods: {
    setChartCategories(data) {
      const categories = [];
      Object.keys(data[0]).forEach((key) => {
        if (
          key === "id" ||
          key === "Hauptartikelnr" ||
          key === "Artikelname" ||
          key === "Beschreibung"
        ) {
          return;
        }
        categories.push(key);
      });
      this.chartCategories = categories;
    },

    updateSelect(e) {
      if (e.target.id === "select1") {
        this.setChartData(
          this.getChartData,
          this.selectedChartCat1,
          e.target.id
        );
      } else if (e.target.id === "select2") {
        this.setChartData(
          this.getChartData,
          this.selectedChartCat2,
          e.target.id
        );
      }
    },

    setChartData(data, cat, id) {
      const counts = data.reduce((acc, el) => {
        acc[el[cat]] = (acc[el[cat]] || 0) + 1;
        return acc;
      }, {});
      Object.keys(counts).forEach((key) => {
        if (key === "") {
          counts["k.A."] = counts[key]; // Assign the value of the empty key to the new key 'k.A.'
          delete counts[key]; // Delete the old key
        }
      });
      if (id === "select1") {
        this.pieChartData = Object.entries(counts);
        this.selectedChartCat1;
      } else if (id === "select2") {
        this.columnChartData = Object.entries(counts);
      }
    },
  },
};
</script>

<style scoped>
.chart-container {
  width: 100%;
  padding: 1rem;
  padding-bottom: 2rem;
  display: grid;
  grid-template-columns: 1fr 1fr;
  background: #d3d3d3;
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
  padding: 1rem;
  border-right: 1px solid #222;
}

label {
  color: #2194f0;

  text-transform: uppercase;
  margin-top: 2rem;
  font-size: 1.1rem;
}

#select1,
#select2 {
  padding: 0.5rem 1rem;
  margin: 0 2rem 2rem 2rem;
  /* text-transform: uppercase; */
  color: white;
  background: #222;
  /* border: 1px solid black; */
  text-align: center;
}
</style>
