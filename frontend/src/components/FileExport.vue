<template>
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
        <button @click="cancelExport" class="btn-confirm-export">
          Abbrechen</button
        ><button @click="confirmExport" class="btn-confirm-export">
          Speichern
        </button>
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
      const tableName = this.getTableName; // Assuming this.getTableName retrieves the correct table name
      try {
        const response = await this.exportData(tableName);
        if (!response.success) {
          console.error("Export failed:", response.error);
          // Handle export failure (e.g., show a notification to the user)
        } else {
          this.setSuccessCode("FES05");
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
<style scoped>
.export-confirm {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 100%;
  height: 100%;
  position: fixed;
  top: 0;
  left: 0;
  backdrop-filter: blur(10px);
  z-index: 1;
}

.confirm-container {
  padding: 2rem;
  display: flex;
  justify-content: center;
  flex-direction: column;
  align-items: center;
  background: rgba(255, 255, 255, 0.7);
  border-radius: 10px;
}

.confirm-info {
  color: #222;
  padding: 0.5rem;
  /* text-transform: uppercase; */
  font-size: 1rem;
}

.btn-confirm-container {
  margin-top: 2rem;
}

.btn-confirm-export {
  width: 170px;
  padding: 0.5rem 1rem;
  margin: 0.5rem;
  text-transform: uppercase;
  background: #222;
  color: #2194f0;
  font-size: 1rem;
  transition: all 0.3s;
}

.btn-confirm-export:hover {
  background: #444;
  color: #eee;
}
</style>
