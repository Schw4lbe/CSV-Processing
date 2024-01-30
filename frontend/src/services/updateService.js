const baseURL = "http://localhost/external/api/update.api.php";

export const fetchData = async (tableName) => {
  try {
    const response = await fetch(
      `${baseURL}/get?tableName=${encodeURIComponent(tableName)}`,
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
