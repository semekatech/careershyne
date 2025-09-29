<template>
  <section
    class="relative bg-gradient-to-br from-green-50 via-white to-purple-100"
  >
    <div
      class="max-w-7xl mx-auto px-6 lg:px-8 py-20 grid lg:grid-cols-2 gap-12 items-center"
    >
      <!-- LEFT CONTENT -->
      <div>
        <p
          class="text-sm font-semibold text-purple-600 uppercase tracking-wide"
        >
          FREE AI RESUME REVIEWER
        </p>
        <h1 class="mt-4 text-4xl font-extrabold text-gray-900 sm:text-5xl">
          Your next job is waiting. Is your resume ready?
        </h1>
        <p class="mt-6 text-lg text-gray-600 max-w-lg">
          Get an instant, free AI analysis of your CV. We check to make sure
          your document is optimized and ready to land more interviews.
        </p>

        <!-- 🔥 CTA Upload Box -->
        <div
          class="mt-10 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl p-8 shadow-xl"
        >
          <h3 class="text-xl font-bold">
            🚀 Upload your resume & get instant feedback
          </h3>
          <p class="mt-2 text-indigo-100 text-sm">
            PDF & DOCX supported · Max file size 2MB · 100% private
          </p>

          <div class="mt-6 flex flex-col sm:flex-row gap-4">
            <router-link
              to="/free-review"
              class="px-6 py-3 bg-white text-indigo-700 font-semibold rounded-lg shadow hover:bg-gray-100 transition"
            >
              Upload My Resume
            </router-link>

            <button
              class="px-6 py-3 border border-white/70 text-white font-medium rounded-lg hover:bg-white/10 transition"
            >
              Try Demo First
            </button>
          </div>
        </div>
      </div>

      <!-- RIGHT SIDE IMAGE -->
      <div class="hidden lg:block">
        <img
          src="/img/ai-hero.png"
          alt="Resume Score Preview"
          class="rounded-2xl shadow-xl"
        />
      </div>
    </div>
  </section>
</template>
