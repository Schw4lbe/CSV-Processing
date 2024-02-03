const baseURL = "http://localhost/external/api/export.api.php";

export const csvExport = async (tableName) => {
  console.log("tableName: ", tableName);
  try {
    const response = await fetch(
      `${baseURL}?tableName=${encodeURIComponent(tableName)}`,
      {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
        },
      }
    );
    if (!response.ok) {
      throw new Error("Network error while exporting CSV!");
    }
    return await response.json();
  } catch (error) {
    console.error("Error in csvExport service:", error);
    throw error;
  }
};
