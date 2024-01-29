<template>
  <div class="home-wrapper">
    <FileUpload />
    <DataTable />
    <LineChart />
  </div>
</template>

<script>
import { defineComponent } from "vue";

// Components
import FileUpload from "../components/FileUpload.vue";
import DataTable from "../components/DataTable.vue";
import LineChart from "../components/LineChart.vue";

export default defineComponent({
  name: "HomeView",

  components: {
    FileUpload,
    DataTable,
    LineChart,
  },
});
</script>
