<template>
  <section class="bg-gradient-to-br from-indigo-50 via-white to-teal-50 py-16">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
      
      <!-- HEADER -->
      <div class="text-center mb-12">
        <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900">
          Success Stories
        </h2>
        <p class="mt-3 text-lg text-gray-600">
          See how our AI-powered resume checker transforms careers.
        </p>
      </div>

      <div class="grid md:grid-cols-2 gap-10 items-center">
        
        <!-- LEFT: BEFORE & AFTER CARD -->
        <div class="space-y-6">
          <!-- BEFORE -->
          <div class="bg-red-50 border border-red-200 rounded-xl p-6 shadow">
            <h3 class="text-xl font-semibold text-red-700 mb-4">
              🚫 Before CareerShyne AI
            </h3>
            <ul class="space-y-3 text-gray-700">
              <li class="flex items-start">
                <span class="text-red-500 text-lg mr-2">✖</span>
                Resume lacked relevant keywords and impact.
              </li>
              <li class="flex items-start">
                <span class="text-red-500 text-lg mr-2">✖</span>
                No interview callbacks despite multiple applications.
              </li>
            </ul>
          </div>

          <!-- AFTER -->
          <div class="bg-green-50 border border-green-200 rounded-xl p-6 shadow">
            <h3 class="text-xl font-semibold text-green-700 mb-4">
              ✅ After CareerShyne AI
            </h3>
            <ul class="space-y-3 text-gray-700">
              <li class="flex items-start">
                <span class="text-green-600 text-lg mr-2">✔</span>
                Resume optimized to match job descriptions in minutes.
              </li>
              <li class="flex items-start">
                <span class="text-green-600 text-lg mr-2">✔</span>
                Landed a job interview and secured a full-time offer.
              </li>
            </ul>
          </div>
        </div>

        <!-- RIGHT: PERSON TESTIMONIAL -->
        <div class="flex flex-col items-center md:items-start text-center md:text-left">
          <img
            src="/img/user.webp"
            alt="User Success"
            class="w-40 h-40 rounded-full border-4 border-indigo-200 shadow-lg mb-4"
          />
          <h4 class="text-2xl font-bold text-gray-900">Meet George</h4>
          <p class="mt-2 text-lg text-indigo-600 font-medium italic">
            “CareerShyne AI gave me the confidence to land my dream job in just 2 days.”
          </p>
        </div>
      </div>
    </div>
  </section>
</template>
