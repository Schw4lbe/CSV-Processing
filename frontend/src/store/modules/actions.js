import { uploadCsvData } from "@/services/uploadService";
import { fetchData } from "@/services/updateService";

export default {
  async uploadCsv({ commit }, uploadData) {
    try {
      const response = await uploadCsvData(uploadData);
      if (response.success) {
        commit("setUploadSuccessMsg", response.message);
        // catch console.log and write to local storage for later use in fetch data
        console.log(response.tableName);
        return response;
      } else {
        return { success: false };
      }
    } catch (error) {
      console.error("Error in uploadCsv action:", error);
      throw error;
    }
  },

  async fetchFormData(_, formData) {
    try {
      const response = await fetchData(formData);
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
