<template>
  <div class="p-6 max-w-3xl mx-auto">
    <h1 class="text-2xl font-bold mb-4 text-gray-800">My Profile</h1>

    <!-- Loading / Error -->
    <p v-if="loading" class="text-gray-500">Loading profile...</p>
    <p v-if="error" class="text-red-500">{{ error }}</p>

    <!-- Profile details -->
    <div v-if="profile" class="space-y-6">
      <div>
        <h2 class="text-lg font-semibold text-gray-700">Industry</h2>
        <p class="text-gray-800">{{ profile.industry?.name || "Not set" }}</p>
      </div>

      <div>
        <h2 class="text-lg font-semibold text-gray-700">Education Level</h2>
        <p class="text-gray-800">{{ profile.education_level?.name || "Not set" }}</p>
      </div>

      <div>
        <h2 class="text-lg font-semibold text-gray-700">County</h2>
        <p class="text-gray-800">{{ profile.county?.name || "Not set" }}</p>
      </div>

      <div>
        <h2 class="text-lg font-semibold text-gray-700">CV</h2>
        <div v-if="profile.cv_url">
          <a
            :href="profile.cv_url"
            target="_blank"
            class="text-orange-600 underline"
          >
            Download CV
          </a>
        </div>
        <p v-else class="text-gray-500">No CV uploaded</p>
      </div>

      <div>
        <h2 class="text-lg font-semibold text-gray-700">Cover Letter</h2>
        <div v-if="profile.cover_letter_url">
          <a
            :href="profile.cover_letter_url"
            target="_blank"
            class="text-orange-600 underline"
          >
            Download Cover Letter
          </a>
        </div>
        <p v-else class="text-gray-500">No Cover Letter uploaded</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from "vue";
import { storeToRefs } from "pinia";
import { useProfileStore } from "@/stores/profile";

const profileStore = useProfileStore();
const { profile, loading, error } = storeToRefs(profileStore);

onMounted(() => {
  profileStore.fetchProfile();
});
</script>
