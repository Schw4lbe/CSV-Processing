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
  },

  // mounted() {
  //   this.loadItems({ page: 1, itemsPerPage: this.itemsPerPage, sortBy: [] });
  // },

  methods: {
    ...mapActions(["fetchFormData"]),

    async loadItems({ page, itemsPerPage, sortBy }) {
      this.loading = true;
      const payload = {
        tableName: this.getTableName,
        page,
        itemsPerPage,
        sortBy: sortBy.length ? sortBy[0] : { key: "default", order: "asc" },
      };

      try {
        const response = await this.fetchFormData(payload);
        if (response && response.success) {
          console.log(response);
          this.serverItems = response.tableData;
          this.totalItems = response.tableData.length;
          // this.totalItems = response.totalItems;
          this.loading = false;
          this.setTableHeaders(response.tableData[0]);
          // this.items = response.tableData;
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
