<template>
  <v-data-table-server
    v-model:items-per-page="itemsPerPage"
    :items-per-page-options="[5, 10, 20, 50, 100, 200]"
    :headers="visibleHeaders"
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
                  <!-- for loop for dynamic content rendering -->
                  <v-col
                    v-for="(value, key) in editedItem"
                    :key="key"
                    cols="12"
                    sm="6"
                    md="4"
                  >
                    <!-- due to long content textareas are displayed for those two keys -->
                    <template
                      v-if="key === 'Beschreibung' || key === 'Materialangaben'"
                    >
                      <v-textarea
                        v-model="editedItem[key]"
                        :label="key"
                        :disabled="key === 'id'"
                        auto-grow
                        full-width
                      ></v-textarea>
                    </template>

                    <!-- all other keys will get regular text boxes -->
                    <template v-else>
                      <v-text-field
                        v-model="editedItem[key]"
                        :label="key"
                        :disabled="key === 'id'"
                        full-width
                      ></v-text-field>
                    </template>
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

    <!-- limit content size for table view to 50 chars -->
    <template v-slot:[`item.Beschreibung`]="{ item }">
      {{ truncateText(item.Beschreibung) }}
    </template>
    <template v-slot:[`item.Materialangaben`]="{ item }">
      {{ truncateText(item.Materialangaben) }}
    </template>

    <template v-slot:[`item.actions`]="{ item }">
      <v-icon small class="mr-2" @click="editItem(item)"> mdi-pencil </v-icon>
      <v-icon small @click="deleteItem(item)"> mdi-delete </v-icon>
    </template>

    <template v-slot:no-data>
      <v-btn color="primary" @click="loadItems"> Reset </v-btn>
    </template>

    <template v-slot:loading>
      <v-skeleton-loader
        v-for="i in itemsPerPage === -1 ? totalItems : itemsPerPage"
        :key="`skeleton-row-${i}`"
        type="table-row"
        :headers="headers"
      />
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
    currentPage: 1,
    itemsPerPage: 10,
    currentSort: [{ key: "id", order: "asc" }],

    loading: false,
    dialog: false,
    dialogDelete: false,

    editedIndex: -1,
    editedItem: {},
    defaultItem: {},
  }),

  computed: {
    ...mapGetters(["getTableName"]),

    visibleHeaders() {
      return this.headers.filter((header) => header.visible);
    },

    watchTableNameSet() {
      return this.getTableName;
    },

    formTitle() {
      return this.editedIndex === -1 ? "New Item" : "Edit Item";
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
    ...mapActions(["fetchFormData", "updateItem", "addNewItem", "removeItem"]),

    truncateText(text) {
      return text && text.length > 50 ? text.substr(0, 50) + "..." : text;
    },

    editItem(item) {
      this.editedIndex = this.serverItems.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialog = true;
    },

    deleteItem(item) {
      this.editedIndex = this.serverItems.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialogDelete = true;
    },

    async deleteItemConfirm() {
      const itemId = this.serverItems[this.editedIndex].id;
      try {
        const response = await this.removeItem(itemId);
        if (response && response.success) {
          this.loadItems();
        }
      } catch (error) {
        console.error("Error in remove item method.");
        throw error;
      }
      this.closeDelete();
    },

    close() {
      this.dialog = false;
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      });
    },

    closeDelete() {
      this.dialogDelete = false;
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      });
    },

    async save() {
      if (this.editedIndex > -1) {
        const item = Object.assign(
          this.serverItems[this.editedIndex],
          this.editedItem
        );
        try {
          const response = await this.updateItem(item);
          if (response && response.success) {
            this.loadItems();
          }
        } catch (error) {
          console.error("Error in save updated item method.", error);
          throw error;
        }
      } else {
        try {
          const response = await this.addNewItem(this.editedItem);
          if (response && response.success) {
            this.loadItems();
          }
        } catch (error) {
          console.error("Error in save new item method.", error);
          throw error;
        }
      }
      this.close();
    },

    async loadItems({
      page = this.currentPage,
      itemsPerPage = this.itemsPerPage,
      sortBy = this.currentSort,
    } = {}) {
      // scrolls back up whenever a page or itemperpage change happens
      window.scrollTo(0, 0);

      // cache current info to prevent resetting pagination for better user experience
      this.currentPage = page;
      this.itemsPerPage = itemsPerPage;
      this.currentSort = sortBy;

      // guard to prevent fetching data without initial table creation
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

      // introduce dev timeout to simulate server latency to visualize loading animation
      setTimeout(async () => {
        try {
          const response = await this.fetchFormData(payload);
          if (response && response.success) {
            this.serverItems = response.tableData;
            this.totalItems = response.total;

            // reset headers to prevent duplicates
            this.headers = [];
            this.setTableHeaders(response.tableData[0]);
          }
        } catch (error) {
          console.error("error:", error);
        } finally {
          this.loading = false;
        }
      }, 500); // Delay set for 500ms
    },

    setTableHeaders(obj) {
      //guard to prevent error while deleting last item on page
      if (obj === undefined) {
        return;
      }

      const keys = Object.keys(obj);
      keys.forEach((key) => {
        const newObj = {};

        // shorten column name
        if (key === "Hauptartikelnr") {
          newObj.title = "Art#";
        } else if (key === "Geschlecht") {
          newObj.title = "Gender";
        } else {
          newObj.title = key;
        }

        newObj.key = key;
        newObj.sortable = false;

        // decide visebility in table overview
        if (key === "id" || key === "Bildname") {
          newObj.visible = false;
        } else {
          newObj.visible = true;
        }
        this.headers.push(newObj);
      });

      // add actions column
      this.headers.push({
        title: "Aktionen",
        key: "actions",
        sortable: false,
        visible: true,
      });

      // guard to set initial default values for ui management
      if (Object.keys(this.editedItem).length === 0) {
        this.setEditItemDefault(keys);
      }
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
