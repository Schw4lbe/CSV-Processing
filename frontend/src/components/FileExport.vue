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
      const tableName = this.getTableName; // Assuming this.getTableName retrieves the correct table name
      try {
        const response = await this.exportData(tableName);
        if (!response.success) {
          console.error("Export failed:", response.error);
          // Handle export failure (e.g., show a notification to the user)
        }
        // No need to handle data for download here since the service does it
      } catch (error) {
        console.error("Error in handleExportData method:", error);
        // Handle any additional errors
      }
    },
  },
};
</script>
