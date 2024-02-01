<template>
  <v-data-table-server
    v-model:items-per-page="itemsPerPage"
    :headers="headers"
    :items-length="itemsLength"
    :items="items"
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
    items: [],
    itemsLength: 0,
    itemsPerPage: 5,
    loading: false,
  }),

  computed: {
    ...mapGetters(["getTableName"]),
  },

  mounted() {
    this.loadItems({ page: 1, itemsPerPage: this.itemsPerPage, sortBy: [] });
  },

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
          this.items = response.tableData;
          this.itemsLength = response.totalItems;
          this.loading = false;
          // this.setTableHeaders(response.tableData[0]);
          // this.items = response.tableData;
        }
      } catch (error) {
        console.error("error:", error);
        this.loading = false;
        throw error;
      }
    },

    // move to mutation after receiving data from backend
    // setTableHeaders(obj) {
    //   const keys = Object.keys(obj);

    //   keys.forEach((key) => {
    //     const newObj = {};
    //     newObj.title = key;
    //     newObj.value = key;
    //     this.headers.push(newObj);
    //   });
    // },
  },
};
</script>
