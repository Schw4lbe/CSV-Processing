<template>
  <div class="home-wrapper">
    <FileUpload />
    <DataChart />
    <ServerDataTable />
    <FileExport />
    <UiMsgModal />
  </div>
</template>

<script>
import { defineComponent } from "vue";

// Components
import FileUpload from "../components/FileUpload.vue";
import ServerDataTable from "../components/ServerDataTable.vue";
import FileExport from "../components/FileExport.vue";
import DataChart from "../components/DataChart.vue";

// UI updates
import UiMsgModal from "@/components/UiMsgModal.vue";

export default defineComponent({
  name: "HomeView",

  components: {
    FileUpload,
    ServerDataTable,
    FileExport,
    DataChart,
    UiMsgModal,
  },
});
</script>
