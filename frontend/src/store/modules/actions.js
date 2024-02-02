import { uploadCsvData } from "@/services/uploadService";
import { fetchData } from "@/services/fetchService";

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

  async updateItem(_, item) {
    console.log("item in updateItem action: ", item);
    // temp
    const response = { success: true };
    return response;
  },

  async addNewItem(_, item) {
    console.log("item in addNewItem action: ", item);
    // temp
    const response = { success: true };
    return response;
  },
};
