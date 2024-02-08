const baseURL = "http://localhost/external/api/export.api.php";

export const csvExport = async (tableName) => {
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
    } else if (
      response.ok &&
      response.headers.get("Content-Type").includes("text/csv")
    ) {
      const blob = await response.blob();
      const downloadUrl = window.URL.createObjectURL(blob);
      const a = document.createElement("a");
      a.href = downloadUrl;
      a.download = "export.csv";
      document.body.appendChild(a);
      a.click();
      a.remove();
      return { success: true };
    } else {
      return { success: false };
    }
  } catch (error) {
    console.error("Error in csvExport service:", error);
    throw error;
  }
};
