<template>
  <v-btn v-if="getTableName" @click="handleExportData">Export Data</v-btn>
</template>
<script>
import { mapActions, mapGetters } from "vuex";

export default {
  name: "FileExport",

  computed: {
    ...mapGetters(["getTableName"]),
  },

  methods: {
    ...mapActions(["exportData"]),

    async handleExportData() {
      const tableName = this.getTableName;
      try {
        const response = await this.exportData(tableName);
        if (response && response.success) {
          // handle data for download
        }
      } catch (error) {
        console.error("Error in handleExportData method:", error);
        throw error;
      }
    },
  },
};
</script>
