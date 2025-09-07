import axios from "axios";

export const logVisitor = async () => {
  try {
    const res = await axios.post("https://careershyne.com/api/log-visitor", {
      page,
    });
    F;
    return res.data;
  } catch (error) {
    console.error("Error logging visitor:", error);
    throw error;
  }
};
