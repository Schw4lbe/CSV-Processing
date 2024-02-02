const baseURL = "http://localhost/external/api/crud.api.php";

export const updateItem = async (item) => {
  console.log("item in service: ", item);
  try {
    const response = await fetch(`${baseURL}`, {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(item),
    });
    if (!response.ok) {
      throw new Error("Network error while updating Item!");
    }
    return await response.json();
  } catch (error) {
    console.error("Error in updateItem service:", error);
    throw error;
  }
};
