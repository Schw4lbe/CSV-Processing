const baseURL = `${process.env.VUE_APP_API_BASE_URL}/api/drop.api.php`;

export const dropTable = async (tableName) => {
  try {
    const response = await fetch(`${baseURL}`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(tableName),
    });
    if (!response.ok) {
      throw new Error("Network error while dropping table!");
    }
    return await response.json();
  } catch (error) {
    console.error("Error in dropTable service:", error);
    throw error;
  }
};
