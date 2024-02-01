const baseURL = "http://localhost/external/api/fetch.api.php";

export const fetchData = async (payload) => {
  try {
    const response = await fetch(
      `${baseURL}?tableName=${encodeURIComponent(
        payload.tableName
      )}&page=${encodeURIComponent(
        payload.page
      )}&itemsPerPage=${encodeURIComponent(
        payload.itemsPerPage
      )}&sortBy=${encodeURIComponent(JSON.stringify(payload.sortBy))}`,
      {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
        },
      }
    );

    const responseData = await response.json(); // Parse JSON response

    if (!response.ok || !responseData.success) {
      throw new Error(
        responseData.message || "Network error while fetching table data!"
      );
    }
    return responseData; // return the successful response data
  } catch (error) {
    console.error("Error in fetchData service:", error);
    throw error; // Propagate the error
  }
};
