<template>
  <div>
    <JobsList
      :jobs="jobs"
      :loading="loading"
      @view-details="openModal"
      @check-eligibility="openEligibility"
      @cv-revamp="openCvRevamp"
      @generate-cover="$emit('generate-cover', $event)"
      @generate-email="$emit('generate-email', $event)"
    />

    <JobDetailsModal
      v-if="showModal"
      :job="selectedJob"
      @close="closeModal"
    />

    <EligibilityModal
      v-if="showEligibilityModal"
      :job="selectedJob"
      :progress="eligibilityProgress"
      :result="eligibilityResult"
      @close="closeEligibility"
    />

    <CvRevampModal
      v-if="showCvRevampModal"
      :job="selectedJob"
      :progress="cvRevampProgress"
      :result="cvRevampResult"
      @close="closeCvRevamp"
      @download="downloadWord"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useJobs } from "@/composables/useJobs";
import { useEligibility } from "@/composables/useEligibility";
import { useCvRevamp } from "@/composables/useCvRevamp";
import JobDetailsModal from "@/components/Dashboard/modals/JobDetailsModal.vue";
import EligibilityModal from "@/components/Dashboard/modals/EligibilityModal.vue";
import CvRevampModal from "@/components/Dashboard/modals/CvRevampModal.vue";

const { jobs, loading, fetchJobs } = useJobs();
const { showEligibilityModal, eligibilityProgress, eligibilityResult, openEligibility, closeEligibility } = useEligibility();
const { showCvRevampModal, cvRevampProgress, cvRevampResult, openCvRevamp, closeCvRevamp, downloadWord } = useCvRevamp();
const showModal = ref(false);
const selectedJob = ref(null);

function openModal(job) {
  selectedJob.value = job;
  showModal.value = true;
}

function closeModal() {
  selectedJob.value = null;
  showModal.value = false;
}

onMounted(fetchJobs);
</script>
