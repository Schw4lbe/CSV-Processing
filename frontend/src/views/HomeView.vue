<template>
  <div class="home-wrapper">
    <FileUpload />
    <DataTable />
  </div>
</template>

<script>
import { defineComponent } from "vue";

// Components
import FileUpload from "../components/FileUpload.vue";
import DataTable from "../components/DataTable.vue";

export default defineComponent({
  name: "HomeView",

  components: {
    FileUpload,
    DataTable,
  },
});
</script>
