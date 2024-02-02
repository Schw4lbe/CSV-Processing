<template>
  <v-data-table-server
    v-model:items-per-page="itemsPerPage"
    :headers="headers"
    :items-length="totalItems"
    :items="serverItems"
    :loading="loading"
    @update:options="loadItems"
    class="elevation-1"
  >
    <template v-slot:top>
      <v-toolbar flat>
        <v-toolbar-title>My CRUD</v-toolbar-title>
        <v-divider class="mx-4" inset vertical></v-divider>
        <v-spacer></v-spacer>

        <v-dialog v-model="dialog" min-width="90%">
          <template v-slot:activator="{ props }">
            <v-btn color="primary" dark class="mb-2" v-bind="props">
              New Item
            </v-btn>
          </template>
          <v-card>
            <v-card-title>
              <span class="text-h5">{{ formTitle }}</span>
            </v-card-title>

            <v-card-text>
              <v-container>
                <v-row>
                  <v-col
                    v-for="(value, key) in editedItem"
                    :key="key"
                    cols="12"
                    sm="6"
                    md="4"
                  >
                    <v-text-field
                      v-model="editedItem[key]"
                      :label="key"
                    ></v-text-field>
                  </v-col>
                </v-row>
              </v-container>
            </v-card-text>

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="blue-darken-1" variant="text" @click="close">
                Cancel
              </v-btn>
              <v-btn color="blue-darken-1" variant="text" @click="save">
                Save
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
        <v-dialog v-model="dialogDelete" max-width="500px">
          <v-card>
            <v-card-title class="text-h5"
              >Are you sure you want to delete this item?</v-card-title
            >
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="blue-darken-1" variant="text" @click="closeDelete"
                >Cancel</v-btn
              >
              <v-btn
                color="blue-darken-1"
                variant="text"
                @click="deleteItemConfirm"
                >OK</v-btn
              >
              <v-spacer></v-spacer>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </v-toolbar>
    </template>

    <template v-slot:[`item.actions`]="{ item }">
      <v-icon small class="mr-2" @click="editItem(item)"> mdi-pencil </v-icon>
      <v-icon small @click="deleteItem(item)"> mdi-delete </v-icon>
    </template>

    <template v-slot:no-data>
      <v-btn color="primary" @click="loadItems"> Reset </v-btn>
    </template>
  </v-data-table-server>
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

    dialog: false,
    dialogDelete: false,

    editedIndex: -1,
    editedItem: {},
    defaultItem: {},
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

    // ui control dialogs
    dialog(val) {
      val || this.close();
    },
    dialogDelete(val) {
      val || this.closeDelete();
    },
  },

  methods: {
    ...mapActions(["fetchFormData"]),

    editItem(item) {
      this.editedIndex = this.serverItems.indexOf(item);
      this.editedItem = Object.assign({}, item);
      console.log("Edit item:", item);
      this.dialog = true;
    },

    deleteItem(item) {
      this.editedIndex = this.serverItems.indexOf(item);
      this.editedItem = Object.assign({}, item);
      console.log("Delete item:", item);
      this.dialogDelete = true;
    },

    deleteItemConfirm() {
      this.serverItems.splice(this.editedIndex, 1);
      // TODO: delete mechanic for backend on click send request to backend for deletion
      // after deletion update frontend and best case pagination and page stays the same
      // to be defined in detail
      this.closeDelete();
    },

    close() {
      this.dialog = false;
      console.log("close");
      // temp next tick default value to prefent frontend bugs
      // to be replaced with async await backend calls and will be replaced then
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      });
    },

    closeDelete() {
      this.dialogDelete = false;
      console.log("closeDelete");
      // temp next tick default value to prefent frontend bugs
      // to be replaced with async await backend calls and will be replaced then
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      });
    },

    save() {
      if (this.editedIndex > -1) {
        Object.assign(this.serverItems[this.editedIndex], this.editedItem);
        // TODO: update mechanic for backend on click send data then update table
        // best case would be to stay in the same pagination so user dont has to navigate again
        // to be defined in detail
      } else {
        this.serverItems.push(this.editedItem);
        // TODO: on creating a new item send data to backend
        // verify Data in Backend and update table
        // to be defined in detail
      }
      this.close();
    },

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

    setTableHeaders(obj) {
      const keys = Object.keys(obj);

      keys.forEach((key) => {
        const newObj = {};
        newObj.title = key;
        newObj.key = key;
        this.headers.push(newObj);
      });

      // add actions column
      this.headers.push({
        title: "Aktionen",
        key: "actions",
        sortable: false,
      });

      // guard to set initial default values for ui management
      if (Object.keys(this.editedItem).length === 0) {
        this.setEditItemDefault(keys);
      }

      console.log("editedItem: ", this.editedItem);
    },

    setEditItemDefault(keys) {
      keys.forEach((key) => {
        // exclude id from editing and creating -> auto increment in backend
        if (key === "id") {
          return;
        }
        this.editedItem[key] = "";
        this.defaultItem[key] = "";
      });
    },
  },
};
</script>
