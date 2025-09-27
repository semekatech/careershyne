import { ref } from "vue";
import cvRevampService from "@/services/cvRevamp";

export function useCvRevamp() {
  const showCvRevampModal = ref(false);
  const cvRevampProgress = ref(0);
  const cvRevampResult = ref(null);

  async function openCvRevamp(job) {
    showCvRevampModal.value = true;
    cvRevampProgress.value = 0;
    cvRevampResult.value = null;

    try {
      const interval = setInterval(() => {
        if (cvRevampProgress.value < 90) cvRevampProgress.value += 10;
      }, 400);

      const result = await cvRevampService.revamp(job.id);
      clearInterval(interval);
      cvRevampProgress.value = 100;
      cvRevampResult.value = result;
    } catch (err) {
      cvRevampProgress.value = 100;
      cvRevampResult.value = { error: "Failed to revamp CV." };
    }
  }

  function closeCvRevamp() {
    showCvRevampModal.value = false;
    cvRevampProgress.value = 0;
    cvRevampResult.value = null;
  }

  function downloadWord() {
    if (!cvRevampResult.value?.revampedCv) return;

    const content = `
      <html xmlns:o="urn:schemas-microsoft-com:office:office" 
            xmlns:w="urn:schemas-microsoft-com:office:word" 
            xmlns="http://www.w3.org/TR/REC-html40">
        <head><meta charset="utf-8"><title>CV</title></head>
        <body>${cvRevampResult.value.revampedCv}</body>
      </html>
    `;
    const blob = new Blob(["\ufeff", content], { type: "application/msword" });
    const url = URL.createObjectURL(blob);
    const link = document.createElement("a");
    link.href = url;
    link.download = "revamped-cv.doc";
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  }

  return { showCvRevampModal, cvRevampProgress, cvRevampResult, openCvRevamp, closeCvRevamp, downloadWord };
}
