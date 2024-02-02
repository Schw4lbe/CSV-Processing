const baseURL = "http://localhost/external/api/crud.api.php";

export const updateItem = async (payload) => {
  try {
    const response = await fetch(`${baseURL}`, {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(payload),
    });
    if (!response.ok) {
      throw new Error("Network error while updating item!");
    }
    return await response.json();
  } catch (error) {
    console.error("Error in updateItem service:", error);
    throw error;
  }
};

export const addNewItem = async (payload) => {
  console.log("payload in addNewItem service: ", payload);
  try {
    const response = await fetch(`${baseURL}`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(payload),
    });
    if (!response.ok) {
      throw new Error("Network error while adding new item!");
    }
    return await response.json();
  } catch (error) {
    console.error("Error in addNewItem service:", error);
    throw error;
  }
};
