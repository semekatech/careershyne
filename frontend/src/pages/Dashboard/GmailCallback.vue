<template>
  <div class="flex justify-center items-center h-screen">
    <p>Connecting Gmail...</p>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useToast } from "vue-toast-notification";
const router = useRouter();
const toast = useToast();

onMounted(() => {
  // Parse code and state from URL
  const params = new URLSearchParams(window.location.search);
  const code = params.get('code');
  const state = params.get('state');

  if (!code || !state) {
    toast.error('Missing code or state');
    router.push('/dashboard');
    return;
  }
  fetch(`/api/auth/google/callback?code=${encodeURIComponent(code)}&state=${encodeURIComponent(state)}`)
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        toast.success(data.message);
        router.push('/dashboard');
      } else {
        toast.error(data.message);
        router.push('/dashboard');
      }
    })
    .catch(() => {
      toast.error('Something went wrong');
      router.push('/dashboard');
    });
});
</script>
