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
    // Handle file download here
    const blob = await response.blob();
    const downloadUrl = window.URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = downloadUrl;
    a.download = "export.csv"; // or "export.csv" if you prefer a static name
    document.body.appendChild(a);
    a.click();
    a.remove();
  } catch (error) {
    console.error("Error in csvExport service:", error);
    throw error;
  }
};
