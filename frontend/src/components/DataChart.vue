<template>
  <div class="chart-main-wrapper">
    <!-- exit confirm dialog -->
    <div v-if="exitConfirmPending && getTableName" class="exit-confirm">
      <div class="confirm-container">
        <p class="confirm-info">
          Sind Sie sicher, dass Sie die Bearbeitung beenden möchten?
        </p>
        <p class="confirm-info">
          Alle nicht exportierten Änderungen gehen verloren!
        </p>
        <div class="btn-confirm-container">
          <v-btn @click="cancelExit" class="btn-confirm-exit">Abbrechen</v-btn
          ><v-btn @click="confirmExit" class="btn-confirm-exit">Beenden</v-btn>
        </div>
      </div>
    </div>

    <!-- data chart container -->
    <div v-if="getTableName" class="chart-wrapper">
      <div class="chart-control">
        <div @click="toggleCharts" class="chart-header chart-toggle">
          <span
            :class="{
              'arrow-right': !chartsVisible,
              'arrow-down': chartsVisible,
            }"
          ></span
          ><span class="cart-description">Grafische Auswertung</span>
        </div>
        <button @click="handleExit" class="btn-exit">beenden</button>
      </div>
      <div v-if="chartsVisible" class="chart-container chart-toggle-container">
        <div class="chart-select-container">
          <div v-if="chartCategories.length > 0" class="select-container">
            <div class="select">
              <label for="select1">Kuchen Diagramm</label>
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
              <label for="select2">Balken Diagramm</label>
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
          <pie-chart :data="pieChartData" suffix="%" />
        </div>
        <column-chart :data="columnChartData" suffix="%"></column-chart>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters, mapMutations } from "vuex";

export default {
  name: "LineChart",

  data() {
    return {
      chartsVisible: false,

      chartCategories: [],
      pieChartData: [],
      columnChartData: [],

      selectedChartCat1: "Geschlecht",
      selectedChartCat2: "Grammatur",

      exitConfirmPending: false,
    };
  },

  computed: {
    ...mapGetters(["getChartData", "getTableName"]),
  },

  watch: {
    getChartData(newVal, oldVal) {
      if (newVal === null || newVal === undefined) {
        return;
      }
      if (newVal !== oldVal) {
        this.setChartCategories(newVal);
        this.setChartData(newVal, this.selectedChartCat1, "select1");
        this.setChartData(newVal, this.selectedChartCat2, "select2");
      }
    },
  },

  methods: {
    ...mapActions(["dropTable"]),

    ...mapMutations(["unsetSessionData", "setSuccessCode"]),

    setChartCategories(data) {
      const categories = [];
      if (data.length === 0) {
        this.chartCategories = categories;
        return;
      }
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

    setChartData(data, cat, id) {
      const amount = data.length;
      const itemsObj = data.reduce((acc, el) => {
        acc[el[cat]] = (acc[el[cat]] || 0) + 1;
        return acc;
      }, {});

      if (Object.hasOwnProperty.call(itemsObj, "")) {
        itemsObj["k.A."] = itemsObj[""];
        delete itemsObj[""];
      }

      Object.keys(itemsObj).forEach((key) => {
        itemsObj[key] = ((itemsObj[key] / amount) * 100).toFixed(2);
      });

      if (id === "select1") {
        this.pieChartData = Object.entries(itemsObj);
        this.selectedChartCat1;
      } else if (id === "select2") {
        this.columnChartData = Object.entries(itemsObj);
      }
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

    toggleCharts() {
      this.chartsVisible = !this.chartsVisible;
    },

    handleExit() {
      this.exitConfirmPending = true;
    },

    cancelExit() {
      this.exitConfirmPending = false;
    },

    async confirmExit() {
      const tableName = this.getTableName;
      try {
        const response = await this.dropTable(tableName);
        if (response && response.success) {
          this.unsetSessionData();
          this.setSuccessCode("FES99");
        }
      } catch (error) {
        console.error("Error in confirmExit method:", error);
        throw error;
      } finally {
        this.resetChartData();
      }
    },

    resetChartData() {
      this.chartsVisible = false;
      this.chartCategories = [];
      this.pieChartData = [];
      this.columnChartData = [];
      this.selectedChartCat1 = "Geschlecht";
      this.selectedChartCat2 = "Grammatur";
      this.exitConfirmPending = false;
    },
  },
};
</script>
