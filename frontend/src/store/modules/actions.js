import { uploadCsvData } from "@/services/uploadService";

export default {
  async uploadCsv(_, uploadData) {
    try {
      const response = await uploadCsvData(uploadData);
      if (response.success) {
        // later on trigger ui update in frontend
        // commit(console.log("success"));
        return response;
      } else {
        return { success: false };
      }
    } catch (error) {
      console.error("Error in uploadCsv Action:", error);
      throw error;
    }
  },
};
