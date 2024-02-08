const baseURL = "http://localhost/external/api/upload.api.php";

export const uploadCsvData = async (formData) => {
  try {
    const response = await fetch(`${baseURL}`, {
      method: "POST",
      body: formData,
    });

    const responseData = await response.json();
    if (!response.ok) {
      throw new Error("Network error while uploading CSV file!");
    }

    return responseData;
  } catch (error) {
    console.error("Error on uploadCsvData service:", error);
    throw error;
  }
};
