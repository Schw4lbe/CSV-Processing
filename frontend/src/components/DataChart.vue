<template>
  <div v-if="getTableName" class="chart-wrapper">
    <div class="chart-control">
      <div @click="toggleDiv2Visibility" class="chart-header div1">
        <span
          :class="{
            'arrow-right': !isDiv2Visible,
            'arrow-down': isDiv2Visible,
          }"
        ></span
        ><span class="cart-description">Grafische Darstellung der Daten</span>
      </div>
      <button @click="handleExit" class="btn-exit">beenden</button>
    </div>
    <div v-if="isDiv2Visible" class="chart-container div2">
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
  </div>
</template>

<script>
import { mapGetters, mapMutations } from "vuex";

export default {
  name: "LineChart",

  data() {
    return {
      // ui controll:
      isDiv2Visible: false,

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
      // guard to prevent error on local storage variable remove on exit
      if (newVal === null || newVal === undefined) {
        return;
      }

      if (newVal !== oldVal) {
        this.setChartCategories(newVal);
        this.setChartData(newVal, this.selectedChartCat1, "select1");
        this.setChartData(newVal, this.selectedChartCat2, "select2");
        // this.setColumnChartData(newVal);
      }
    },
  },

  methods: {
    ...mapMutations(["unsetSessionData"]),

    toggleDiv2Visibility() {
      this.isDiv2Visible = !this.isDiv2Visible;
    },

    handleExit() {
      console.log("session unset. EXIT!");
      this.unsetSessionData();
    },

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
.chart-control {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
}

.btn-exit {
  padding: 0.5rem;
  text-align: center;
  text-transform: uppercase;
  width: 100px;
  background: rgba(255, 99, 71, 0.7);
  color: #222;
  transition: all 0.3s;
}

.btn-exit:hover {
  background: tomato;
  color: white;
}

.div1,
.div2 {
  cursor: pointer;
}

/* Basic arrow using borders */
.arrow-right,
.arrow-down {
  display: inline-block;
  margin-right: 5px;
  border: solid white;
  border-width: 0 2px 2px 0;
  padding: 3px;
  transition: all 0.3s;
}

.arrow-right {
  transform: rotate(-45deg);
  -webkit-transform: rotate(-45deg);
}

.arrow-down {
  transform: rotate(45deg);
  -webkit-transform: rotate(45deg);
}

.chart-header {
  padding: 0.5rem;
  text-transform: uppercase;
}

.cart-description {
  padding: 0.5rem 1rem;
}

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
  color: white;
  background: #222;
  text-align: center;
}
</style>
