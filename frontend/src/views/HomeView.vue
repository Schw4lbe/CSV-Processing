<template>
  <div class="home-wrapper">
    <FileUpload />
    <ServerDataTable />
    <!-- <LineChart /> -->
  </div>
</template>

<script>
import { defineComponent } from "vue";

// Components
import FileUpload from "../components/FileUpload.vue";
import ServerDataTable from "../components/ServerDataTable.vue";
// import LineChart from "../components/LineChart.vue";

export default defineComponent({
  name: "HomeView",

  components: {
    FileUpload,
    ServerDataTable,
    // LineChart,
  },
});
</script>
