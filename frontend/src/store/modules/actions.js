// Upload service:
import { uploadCsvData } from "@/services/uploadService";

// fetch Data service:
import { fetchData } from "@/services/fetchService";

// CRUD services:
import { updateItem } from "@/services/crudService";

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

  async addNewItem(_, item) {
    console.log("item in addNewItem action: ", item);
    // temp
    const response = { success: true };
    return response;
  },
};
