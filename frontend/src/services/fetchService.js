const baseURL = "http://localhost/external/api/fetch.api.php";

export const fetchData = async (payload) => {
  try {
    const response = await fetch(
      `${baseURL}/fetch?tableName=${encodeURIComponent(
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
    const responseData = await response.json();

    if (!response.ok || !responseData.success) {
      throw new Error(
        responseData.message || "Network error while fetching table data!"
      );
    }
    return responseData;
  } catch (error) {
    console.error("Error in fetchData service:", error);
    throw error;
  }
};

export const fetchSearch = async (payload) => {
  try {
    const response = await fetch(
      `${baseURL}/search?tableName=${encodeURIComponent(
        payload.tableName
      )}&page=${encodeURIComponent(
        payload.page
      )}&itemsPerPage=${encodeURIComponent(
        payload.itemsPerPage
      )}&sortBy=${encodeURIComponent(
        JSON.stringify(payload.sortBy)
      )}&searchCategory=${encodeURIComponent(
        payload.searchCategory
      )}&searchQuery=${encodeURIComponent(payload.searchQuery)}`,
      {
        method: "GET",
      }
    );

    const responseData = await response.json();
    if (!response.ok) {
      throw new Error(
        responseData.message || "Network error while fetching table data!"
      );
    }

    if (!responseData.success) {
      return { success: false };
    }
    return responseData;
  } catch (error) {
    console.error("Error in fetchData service:", error);
    throw error;
  }
};
