import Swal from "sweetalert2";

export function useApiError() {
  function handleError(err) {
    if (err.response) {
      const status = err.response.status;
      const data = err.response.data;

      if (status === 422) {
        Swal.fire({
          icon: "warning",
          title: "Invalid CV",
          text: data.message || "The uploaded file is not valid.",
        });
      } else if (status === 429) {
        Swal.fire({
          icon: "info",
          title: "Slow Down ⏳",
          text: data.message || "Too many uploads, wait a minute.",
        });
      } else {
        Swal.fire({
          icon: "error",
          title: "Error",
          text: data.message || "Something went wrong. Try again!",
        });
      }
    } else {
      Swal.fire({
        icon: "error",
        title: "Network Error",
        text: "Could not reach the server. Check your connection.",
      });
    }
  }

  return { handleError };
}
