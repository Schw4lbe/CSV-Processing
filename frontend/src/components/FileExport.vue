<template>
  <div class="export-wrapper">
    <v-btn
      v-if="getTableName"
      id="btn-export"
      color="primary"
      @click="handleExport"
      >als csv exportieren...</v-btn
    >

    <div v-if="exportConfirmPending" class="export-confirm">
      <div class="confirm-container">
        <p class="confirm-info">
          Möchten Sie den aktuellen Stand als CSV-Datei herunterladen?
        </p>
        <div class="btn-confirm-container">
          <v-btn @click="cancelExport" class="btn-confirm-export">
            Abbrechen</v-btn
          ><v-btn @click="confirmExport" class="btn-confirm-export">
            Speichern
          </v-btn>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { mapActions, mapGetters, mapMutations } from "vuex";

export default {
  name: "FileExport",

  data() {
    return {
      exportConfirmPending: false,
    };
  },

  computed: {
    ...mapGetters(["getTableName"]),
  },

  methods: {
    ...mapActions(["exportData"]),

    ...mapMutations(["setSuccessCode"]),

    handleExport() {
      this.exportConfirmPending = true;
    },

    confirmExport() {
      this.exportConfirmPending = false;
      this.handleExportData();
    },

    cancelExport() {
      this.exportConfirmPending = false;
    },

    async handleExportData() {
      const tableName = this.getTableName;
      try {
        const response = await this.exportData(tableName);
        if (!response.success) {
          console.error("Export failed:", response.error);
        } else {
          this.setSuccessCode("FES05");
        }
      } catch (error) {
        console.error("Error in handleExportData method:", error);
      }
    },
  },
};
</script>
