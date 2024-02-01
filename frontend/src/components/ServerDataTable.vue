<template>
  <v-data-table-server
    v-model:items-per-page="itemsPerPage"
    :headers="headers"
    :items-length="totalItems"
    :items="serverItems"
    :loading="loading"
    @update:options="loadItems"
    class="elevation-1"
  />
</template>

<script>
import { mapActions, mapGetters } from "vuex";

export default {
  data: () => ({
    headers: [],
    serverItems: [],
    totalItems: 0,
    itemsPerPage: 5,
    loading: false,
  }),

  computed: {
    ...mapGetters(["getTableName"]),

    watchTableNameSet() {
      return this.getTableName;
    },
  },

  watch: {
    watchTableNameSet(newTableName, oldTableName) {
      if (newTableName !== oldTableName) {
        this.loadItems();
      }
    },
  },

  methods: {
    ...mapActions(["fetchFormData"]),

    async loadItems({ page, itemsPerPage, sortBy } = {}) {
      // default values to make function call in watcher possible without params
      page = page || 1;
      itemsPerPage = itemsPerPage || this.itemsPerPage;
      sortBy = sortBy || [{ key: "id", order: "asc" }];

      // guard to prevent fetch data without initial table creation
      if (this.getTableName === null) {
        return;
      }

      this.loading = true;
      // reset Items to prevent duplicates
      this.serverItems = [];

      const payload = {
        tableName: this.getTableName,
        page,
        itemsPerPage,
        sortBy: sortBy.length ? sortBy[0] : { key: "id", order: "asc" },
      };

      try {
        const response = await this.fetchFormData(payload);
        if (response && response.success) {
          console.log(response);
          this.serverItems = response.tableData;
          this.totalItems = response.total;
          this.loading = false;

          // reset headers to prevent duplicates
          this.headers = [];
          this.setTableHeaders(response.tableData[0]);
        }
      } catch (error) {
        console.error("error:", error);
        this.loading = false;
        throw error;
      }
    },

    // move to mutation after receiving data from backend#
    // redesign later on functional for now but fetches everytime within the data display process
    setTableHeaders(obj) {
      const keys = Object.keys(obj);

      keys.forEach((key) => {
        const newObj = {};
        newObj.title = key;
        newObj.value = key;
        this.headers.push(newObj);
      });
    },
  },
};
</script>
