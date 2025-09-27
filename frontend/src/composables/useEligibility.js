import { ref } from "vue";
import eligibilityService from "@/services/eligibilityService";

export function useEligibility() {
  const showEligibilityModal = ref(false);
  const eligibilityProgress = ref(0);
  const eligibilityResult = ref(null);

  async function openEligibility(job) {
    showEligibilityModal.value = true;
    eligibilityProgress.value = 0;
    eligibilityResult.value = null;

    try {
      const interval = setInterval(() => {
        if (eligibilityProgress.value < 90) eligibilityProgress.value += 10;
      }, 400);

      const result = await eligibilityService.checkEligibility(job.id);
      clearInterval(interval);
      eligibilityProgress.value = 100;
      eligibilityResult.value = result;
    } catch (error) {
      eligibilityProgress.value = 100;
      eligibilityResult.value = { error: "Failed to check eligibility." };
    }
  }

  function closeEligibility() {
    showEligibilityModal.value = false;
    eligibilityProgress.value = 0;
    eligibilityResult.value = null;
  }

  return { showEligibilityModal, eligibilityProgress, eligibilityResult, openEligibility, closeEligibility };
}
