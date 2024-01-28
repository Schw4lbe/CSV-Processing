const baseURL = "http://localhost/TBD";

export const uploadCsvData = async (formData) => {
  try {
    const response = await fetch(`${baseURL}/upload`, {
      method: "POST",
      body: formData,
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
