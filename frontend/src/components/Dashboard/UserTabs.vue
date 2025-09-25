<template>
  <div>
  <div class="border-b border-gray-200 dark:border-gray-700 mb-8">
    <nav aria-label="Tabs" class="-mb-px flex space-x-8">
      <a
        class="border-primary text-primary whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
        href="#"
        >CV Revamp</a
      >
      <a
        class="border-transparent text-muted-light dark:text-muted-dark hover:text-gray-700 dark:hover:text-gray-200 hover:border-gray-300 dark:hover:border-gray-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
        href="#"
        >Cover Letters</a
      >
      <a
        class="border-transparent text-muted-light dark:text-muted-dark hover:text-gray-700 dark:hover:text-gray-200 hover:border-gray-300 dark:hover:border-gray-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
        href="#"
        >Email Templates</a
      >
      <a
        class="border-transparent text-muted-light dark:text-muted-dark hover:text-gray-700 dark:hover:text-gray-200 hover:border-gray-300 dark:hover:border-gray-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
        href="#"
        >Interviews</a
      >
    </nav>
  </div>
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div
      class="lg:col-span-2 bg-surface-light dark:bg-surface-dark p-6 rounded-lg shadow-sm"
    >
      <h3 class="text-xl font-semibold mb-4">Revamp Your CV with AI</h3>
      <p class="text-muted-light dark:text-muted-dark mb-6">
        Upload your current CV, and our AI will provide suggestions to improve
        it. You can also paste your CV content or start from scratch.
      </p>
      <div
        class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-12 text-center"
      >
        <span class="material-icons-sharp text-5xl text-primary mb-4"
          >upload_file</span
        >
        <p class="font-semibold mb-2">Drag and drop your CV here</p>
        <p class="text-muted-light dark:text-muted-dark text-sm mb-4">
          PDF, DOC, DOCX up to 5MB
        </p>
        <button
          class="bg-gray-200 dark:bg-gray-600 text-text-light dark:text-text-dark font-medium py-2 px-4 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors"
        >
          Or browse files
        </button>
      </div>
      <textarea
        class="w-full mt-6 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg p-4 focus:ring-primary focus:border-primary"
        placeholder="Or paste your CV content here..."
        rows="8"
      ></textarea>
    </div>
    <div class="bg-surface-light dark:bg-surface-dark p-6 rounded-lg shadow-sm">
      <h3 class="text-xl font-semibold mb-4">Recent Activity</h3>
      <div class="space-y-4">
        <div class="flex items-start">
          <div class="p-2 bg-green-100 dark:bg-green-900 rounded-full">
            <span
              class="material-icons-sharp text-green-600 dark:text-green-400"
              >check_circle</span
            >
          </div>
          <div class="ml-4">
            <p class="font-medium">CV for "Software Engineer" revamped</p>
            <p class="text-sm text-muted-light dark:text-muted-dark">
              2 hours ago
            </p>
          </div>
        </div>
        <div class="flex items-start">
          <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-full">
            <span class="material-icons-sharp text-blue-600 dark:text-blue-400"
              >edit</span
            >
          </div>
          <div class="ml-4">
            <p class="font-medium">Cover letter for "Acme Corp" created</p>
            <p class="text-sm text-muted-light dark:text-muted-dark">
              1 day ago
            </p>
          </div>
        </div>
        <div class="flex items-start">
          <div class="p-2 bg-yellow-100 dark:bg-yellow-900 rounded-full">
            <span
              class="material-icons-sharp text-yellow-600 dark:text-yellow-400"
              >history</span
            >
          </div>
          <div class="ml-4">
            <p class="font-medium">Email template "Follow-up" used</p>
            <p class="text-sm text-muted-light dark:text-muted-dark">
              3 days ago
            </p>
          </div>
        </div>
        <a class="text-primary font-medium text-sm hover:underline" href="#"
          >View all activity</a
        >
      </div>
    </div>
  </div></div>
</template>
