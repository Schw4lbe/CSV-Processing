import { uploadCsvData } from "@/services/uploadService";
import { fetchData } from "@/services/updateService";

export default {
  async uploadCsv({ commit, dispatch }, uploadData) {
    try {
      const response = await uploadCsvData(uploadData);
      if (response.success) {
        commit("setUploadSuccessMsg", response.message);
        commit("setTableName", response.tableName);
        dispatch("fetchFormData", response.tableName);
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
};
