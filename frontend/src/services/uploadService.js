const baseURL = "http://localhost/TBD";

export const uploadCsvData = async (uploadData) => {
  try {
    const response = await fetch(`${baseURL}/upload`, {
      method: "POST",
      body: uploadData,
    });
    if (!response.ok) {
      throw new Error("Network error while uploading CSV file!");
    }
    return await response.json();
  } catch (error) {
    console.error("Error on uploadCsvData Service:", error);
    throw error;
  }
};
