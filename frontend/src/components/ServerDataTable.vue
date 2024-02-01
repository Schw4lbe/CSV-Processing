<template>
  <v-data-table-server
    v-model:items-per-page="itemsPerPage"
    :headers="headers"
    :items-length="itemsLength"
    :items="items"
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
  }),

  computed: {
    ...mapGetters(["getTableName"]),
  },

  mounted() {
    this.queryTableData();
  },

  methods: {
    ...mapActions(["fetchFormData"]),

    async queryTableData() {
      const tableName = this.getTableName;
      if (tableName) {
        try {
          const response = await this.fetchFormData(tableName);
          if (response.success) {
            this.setTableHeaders(response.tableData[0]);
            this.items = response.tableData;
          }
        } catch (error) {
          console.error("error:", error);
          throw error;
        }
      } else {
        console.log("no table name set.");
      }
    },

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
