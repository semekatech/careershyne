import { ref } from "vue";
import JobService from "@/services/jobService";

export function useJobs() {
  const jobs = ref([]);
  const loading = ref(true);

  async function fetchJobs() {
    loading.value = true;
    try {
      const data = await JobService.getJobs();
      jobs.value = Array.isArray(data.data) ? data.data : [];
    } catch (err) {
      console.error("Error fetching jobs:", err);
      jobs.value = [];
    } finally {
      loading.value = false;
    }
  }

  return { jobs, loading, fetchJobs };
}
