import axios from "axios";

export const logVisitor = async (page) => {
  try {
    // get client IP from external service
    const ipRes = await axios.get("https://api.ipify.org?format=json");
    const clientIp = ipRes.data.ip;

    // send IP + page to backend
    const res = await axios.post("https://careershyne.com/api/log-visitor", {
      ip: clientIp,
      page: page,
    });

    return res.data;
  } catch (error) {
    console.error("Error logging visitor:", error);
    throw error;
  }
};


