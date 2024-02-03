// Upload service:
import { uploadCsvData } from "@/services/uploadService";

// fetch Data service:
import { fetchData } from "@/services/fetchService";

// CRUD services:
import { updateItem } from "@/services/crudService";
import { addNewItem } from "@/services/crudService";
import { removeItem } from "@/services/crudService";

// export service:
import { csvExport } from "@/services/exportService";

export default {
  async uploadCsv({ commit }, uploadData) {
    try {
      const response = await uploadCsvData(uploadData);
      if (response.success) {
        commit("setUploadSuccessMsg", response.message);
        commit("setTableName", response.tableName);
        // dispatch("fetchFormData", response.tableName);
        return response;
      } else {
        return { success: false };
      }
    } catch (error) {
      console.error("Error in uploadCsv action:", error);
      throw error;
    }
  },

  async fetchFormData(_, payload) {
    try {
      const response = await fetchData(payload);
      if (response.success) {
        return response;
      } else {
        return { success: false };
      }
    } catch (error) {
      console.error("Error in fetchFormData action:", error);
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
      await csvExport(tableName);
      // Assuming success if no error is thrown
      return { success: true };
    } catch (error) {
      console.error("Error in exportData action:", error);
      return { success: false, error: error.message };
    }
  },
};
