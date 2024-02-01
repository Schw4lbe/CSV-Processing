const baseURL = "http://localhost/external/api/fetch.api.php";

export const fetchData = async (payload) => {
  console.log("service:", payload);
  try {
    const response = await fetch(
      `${baseURL}/get?tableName=${encodeURIComponent(
        payload.tableName
      )}&page=${encodeURIComponent(
        payload.page
      )}&itemsPerPage=${encodeURIComponent(
        payload.itemsPerPage
      )}&sortBy=${encodeURIComponent(payload.sortBy)}`,
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
