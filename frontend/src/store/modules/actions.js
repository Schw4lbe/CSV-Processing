// Upload service:
import { uploadCsvData } from "@/services/uploadService";

// fetch Data services:
import { fetchData } from "@/services/fetchService";
import { fetchSearch } from "@/services/fetchService";

// CRUD services:
import { updateItem } from "@/services/crudService";
import { addNewItem } from "@/services/crudService";
import { removeItem } from "@/services/crudService";

// export service:
import { csvExport } from "@/services/exportService";

// drop table service:
import { dropTable } from "@/services/dropTableService";

export default {
  async uploadCsv({ commit }, uploadData) {
    try {
      const response = await uploadCsvData(uploadData);
      if (response.success) {
        commit("setTableName", response.tableName);
        return response;
      } else {
        return response;
      }
    } catch (error) {
      console.error("Error in uploadCsv action:", error);
      throw error;
    }
  },

  async fetchFormData({ commit }, payload) {
    try {
      const response = await fetchData(payload);
      if (response.success) {
        commit("storeChartData", response.tableData);
        return response;
      } else {
        return { success: false };
      }
    } catch (error) {
      console.error("Error in fetchFormData action:", error);
      throw error;
    }
  },

  async fetchSearchData({ commit }, payload) {
    try {
      const response = await fetchSearch(payload);
      if (response.success) {
        commit("storeChartData", response.tableData);
        return response;
      } else {
        return { success: false };
      }
    } catch (error) {
      console.error("Error in fetchSearchData action", error);
      throw error;
    }
  },

  async updateItem({ getters }, item) {
    const payload = { tableName: getters.getTableName, item: item };
    try {
      const response = await updateItem(payload);
      if (response.success) {
        return response;
      } else {
        return { success: false };
      }
    } catch (error) {
      console.error("Error in updateItem action:", error);
      throw error;
    }
  },

  async addNewItem({ getters }, item) {
    const payload = { tableName: getters.getTableName, item: item };
    try {
      const response = await addNewItem(payload);
      if (response.success) {
        return response;
      } else {
        return { success: false };
      }
    } catch (error) {
      console.error("Error in addNewItem action:", error);
      throw error;
    }
  },

  async removeItem({ getters }, itemId) {
    const payload = { tableName: getters.getTableName, itemId: itemId };
    try {
      const response = await removeItem(payload);
      if (response.success) {
        return response;
      } else {
        return { success: false };
      }
    } catch (error) {
      console.error("Error in removeItem action:", error);
      throw error;
    }
  },

  async exportData(_, tableName) {
    try {
      const response = await csvExport(tableName);
      if (response.success) {
        return response;
      } else {
        return { success: false };
      }
    } catch (error) {
      console.error("Error in exportData action:", error);
      throw error;
    }
  },

  async dropTable(_, tableName) {
    try {
      const response = await dropTable(tableName);
      if (response.success) {
        return response;
      } else {
        return { success: false };
      }
    } catch (error) {
      console.error("Error in dropTable action:", error);
      throw error;
    }
  },
};
