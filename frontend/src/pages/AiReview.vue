<template>
  <TheWelcome />
  <AiHero />
  <AiCaption />
  <AiService />
  <AiFAQ />
  <AiCTA />
  <FooterSection />
</template>

<script setup>
import { ref } from "vue";
import TheWelcome from "@/components/TheWelcome.vue";
import AiHero from "@/components/AiHero.vue";
import AiCaption from "@/components/AiCaption.vue";
import AiService from "@/components/AiService.vue";
import AiFAQ from "@/components/AiFAQ.vue";
import FooterSection from "@/components/AiFooter.vue";
import UploadService from "@/services/UploadService";
import AiCTA from "@/components/AiCTA.vue";
const fileInput = ref(null);
const selectedFile = ref(null);
const fileName = ref("");
const progress = ref(0);
const uploading = ref(false);
const review = ref(""); // 🔥 to store AI response

function triggerFileInput() {
  fileInput.value.click();
}

function handleFileUpload(event) {
  const file = event.target.files[0];
  if (!file) return;

  selectedFile.value = file;
  fileName.value = file.name;
  progress.value = 0;
}

async function uploadFile() {
  if (!selectedFile.value) return;

  uploading.value = true;
  progress.value = 0;

  try {
    const response = await UploadService.uploadFile(selectedFile.value, (e) => {
      if (e.lengthComputable) {
        progress.value = Math.round((e.loaded * 100) / e.total);
      }
    });

    console.log("Upload successful:", response.data);
    review.value = response.data.review; // 🔥 show review
  } catch (error) {
    console.error("Upload failed:", error);
    alert("Upload failed!");
  } finally {
    uploading.value = false;
  }
}
</script>
