<template>
  <main
    class="bg-gradient-to-br from-rose-50 to-white dark:from-gray-900 dark:to-gray-800"
  >
    <TheWelcome />
    <cvForm />
    <FooterSection />
  </main>
</template>


<style scoped>
.prose {
  max-width: 100%;
}
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
