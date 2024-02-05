<template>
  <v-data-table-server
    v-model:items-per-page="itemsPerPage"
    :items-per-page-options="[5, 10, 20, 50, 100, 200]"
    :headers="visibleHeaders"
    :items-length="totalItems"
    :items="serverItems"
    :loading="loading"
    @update:options="handleUpdate"
    class="elevation-1"
  >
    <template v-slot:top>
      <v-toolbar flat>
        <v-toolbar-title>PRODUKTE</v-toolbar-title>
        <v-spacer></v-spacer>

        <!-- Search bar -->
        <v-select
          v-if="getTableName"
          v-model="searchCategory"
          :items="searchCategories"
          label="Suchkategorie"
          dense
          hide-details
          outlined
          small
          color="primary"
        ></v-select>
        <v-text-field
          v-if="getTableName"
          v-model="searchQuery"
          append-icon="mdi-magnify"
          label="Suchen"
          single-line
          hide-details
          color="primary"
          :disabled="!searchCategory"
          @keyup.enter="onSubmitSearch"
        ></v-text-field>
        <v-btn
          v-if="getTableName"
          @click="resetSearch"
          color="red-lighten-2"
          :disabled="!searchQuery"
          >Suche zurücksetzen</v-btn
        >

        <v-divider class="mx-4" inset vertical></v-divider>
        <v-spacer></v-spacer>

        <v-dialog v-model="dialog" min-width="90%">
          <template v-slot:activator="{ props }">
            <v-btn v-if="getTableName" color="primary" dark v-bind="props">
              Neuer Artikel
            </v-btn>
          </template>
          <v-card>
            <v-card-title>
              <span color="green-lighten-1" class="text-h6">{{
                formTitle
              }}</span>
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
                        maxlength="255"
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
              <v-btn color="blue-lighten-1" variant="text" @click="close">
                Abbrechen
              </v-btn>
              <v-btn color="blue-lighten-1" variant="text" @click="save">
                Speichern
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
        <v-dialog v-model="dialogDelete" max-width="500px">
          <v-card>
            <v-card-title class="text-h6 text-center"
              >Sind Sie sicher, dass Sie diesen Artikel löschen
              möchten?</v-card-title
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
  name: "ServerDataTable",

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

    // search:
    searchCategories: [],
    searchCategory: "",
    searchQuery: "",
    isSearching: false,
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
      return this.editedIndex === -1 ? "NEUER ARTIKEL" : "ARTIKEL BEARBEITEN";
    },
  },

  watch: {
    watchTableNameSet(newTableName, oldTableName) {
      if (newTableName !== oldTableName) {
        this.handleUpdate();
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
    ...mapActions([
      "fetchFormData",
      "fetchSearchData",
      "updateItem",
      "addNewItem",
      "removeItem",
    ]),

    onSubmitSearch() {
      if (this.searchQuery.length === 0) {
        alert("pls enter search term.");
        return;
      } else {
        this.isSearching = true;
        this.handleUpdate();
      }
    },

    resetSearch() {
      this.searchCategory = "";
      this.searchQuery = "";
      this.isSearching = false;
      this.handleUpdate();
    },

    handleUpdate(options) {
      if (!this.getTableName) {
        return;
      }

      // invoce default values for handleUpdate calls outside of @update event or on initial csv upload
      if (!options) {
        options = {
          page: this.currentPage,
          itemsPerPage: this.itemsPerPage,
          sortBy: this.currentSort.length
            ? this.currentSort
            : [{ key: "id", order: "asc" }],
        };
      }

      // cache current state to prevent resetting pagination for better user experience
      this.currentPage = options.page;
      this.itemsPerPage = options.itemsPerPage;
      this.currentSort = options.sortBy;
      this.loading = true;

      const payload = {
        tableName: this.getTableName,
        page: this.currentPage,
        itemsPerPage: this.itemsPerPage,
        sortBy: options.sortBy.length
          ? options.sortBy[0]
          : { key: "id", order: "asc" },
      };

      if (!this.isSearching) {
        this.loadItemsDefault(payload);
      } else {
        this.loadItemsSearch(payload);
      }
    },

    async loadItemsDefault(payload) {
      console.log("default");
      try {
        const response = await this.fetchFormData(payload);
        if (response && response.success) {
          this.setTableParams(response);
        }
      } catch (error) {
        console.error("error:", error);
      } finally {
        this.loading = false;
        window.scrollTo(0, 0);
      }
    },

    async loadItemsSearch(payload) {
      console.log("search");
      // add missing values to payload
      payload.searchCategory = this.searchCategory;
      payload.searchQuery = this.searchQuery;
      try {
        // TODO: needs to be replaced with actuall query for fetchSearchData
        const response = await this.fetchSearchData(payload);
        if (response && response.success) {
          this.setTableParams(response);
        }
      } catch (error) {
        console.error("error:", error);
      } finally {
        this.loading = false;
        window.scrollTo(0, 0);
      }
    },

    setTableParams(response) {
      this.serverItems = response.tableData;
      this.totalItems = response.total;

      // set table headers if not set before
      if (this.headers.length === 0) {
        this.setTableHeaders(response.tableData[0]);
      }

      // set search categories if not set before
      if (this.searchCategories.length === 0) {
        this.setSearchCategories(response.tableData[0]);
      }
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

        // decide visibility in table overview
        if (key === "id" || key === "Bildname") {
          newObj.visible = false;
        } else {
          newObj.visible = true;
        }
        this.headers.push(newObj);
      });

      // add actions column for edit / delete
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

    setSearchCategories(obj) {
      //guard to prevent error while deleting last item on page
      if (obj === undefined) {
        return;
      }

      const keys = Object.keys(obj);
      keys.forEach((key) => {
        if (key === "id" || key === "Bildname") {
          return;
        }
        this.searchCategories.push(key);
      });
    },

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
          this.handleUpdate();
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
            this.handleUpdate();
          }
        } catch (error) {
          console.error("Error in save updated item method.", error);
          throw error;
        }
      } else {
        try {
          const response = await this.addNewItem(this.editedItem);
          if (response && response.success) {
            this.handleUpdate();
          }
        } catch (error) {
          console.error("Error in save new item method.", error);
          throw error;
        }
      }
      this.close();
    },
  },
};
</script>
<style>
i.mdi-delete:hover {
  color: tomato;
  transition: all 0.1s;
}

i.mdi-pencil:hover {
  color: lightgreen;
  transition: all 0.1s;
}

span.v-btn__content {
  font-size: 1.1rem;
}
</style>
