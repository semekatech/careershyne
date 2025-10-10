import axios from "axios";

export const logVisitor = async (page) => {
  try {
    // get client IP
    const ipRes = await axios.get("https://api.ipify.org?format=json");
    const clientIp = ipRes.data?.ip || "unknown";

    // send IP + page to backend
    const res = await axios.post(
      "https://careershyne.com/api/api/log-visitor",
      { ip: clientIp, page },
      { timeout: 5000 } // optional
    );

    // make sure res.data exists
    if (!res.data) {
      console.warn("Empty response from log-visitor API");
      return { success: false };
    }

    return res.data;
  } catch (error) {
    console.error("Error logging visitor:", error);
    return { success: false }; 
  }
};
