const baseURL = "http://localhost/external/api/update.api.php";

export const fetchData = async (formData) => {
  try {
    const response = await fetch(
      `${baseURL}/fetch/get?tableName=${encodeURIComponent(formData)}`,
      {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
        },
      }
    );
    if (!response.ok) {
      throw new Error("Network error while fetching table data!");
    }
    return await response.json();
  } catch (error) {
    console.error("Error in fetchData service:", error);
    throw error;
  }
};
