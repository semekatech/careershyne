<template>

    <div v-if="showModal" class="fixed z-50 inset-0 overflow-y-auto">
      <div class="flex items-center justify-center min-h-screen px-4">
        <div
          class="fixed inset-0 bg-gray-900 opacity-75"
          @click="showModal = false"
        ></div>
        <div
          class="bg-white rounded-lg shadow-xl sm:max-w-4xl w-full z-50 transform transition-all"
        >
          <div
            class="px-6 pt-5 pb-4 sm:p-6 sm:pb-4 overflow-y-auto max-h-[90vh]"
          >
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="text-sm font-medium text-gray-700"
                  >Campaign Title <span style="color: red">*</span></label
                >
                <input
                  v-model="form.title"
                  type="text"
                  required
                  placeholder="e.g., Summer Skincare Launch"
                  class="w-full rounded bg-gray-100 p-2 mt-1 outline-none"
                />
                <p v-if="errors.title" class="text-red-600 mt-1 text-sm">
                  {{ errors.title[0] }}
                </p>
              </div>
              <div>
                <label class="text-sm font-medium text-gray-700"
                  >Brand Name <span style="color: red">*</span></label
                >
                <input
                  v-model="form.brand"
                  type="text"
                  required
                  placeholder="Your Company Name"
                  class="w-full rounded bg-gray-100 p-2 mt-1 outline-none"
                />
                <p v-if="errors.brand" class="text-red-600 mt-1 text-sm">
                  {{ errors.brand[0] }}
                </p>
              </div>
              <div class="md:col-span-2">
                <label class="text-sm font-medium text-gray-700"
                  >Company Website</label
                >
                <input
                  v-model="form.website"
                  type="url"
                  placeholder="https://www.yourbrand.com"
                  class="w-full rounded bg-gray-100 p-2 mt-1 outline-none"
                />
                <p v-if="errors.website" class="text-red-600 mt-1 text-sm">
                  {{ errors.website[0] }}
                </p>
              </div>
              <div class="md:col-span-2">
                <label class="text-sm font-medium text-gray-700"
                  >About the Company/Brand
                  <span style="color: red">*</span></label
                >
                <textarea
                  v-model="form.about"
                  rows="3"
                  required
                  placeholder="Describe your company, its mission, and values."
                  class="w-full rounded bg-gray-100 p-2 mt-1 outline-none"
                ></textarea>
                <p v-if="errors.about" class="text-red-600 mt-1 text-sm">
                  {{ errors.about[0] }}
                </p>
              </div>
              <div>
                <label class="text-sm font-medium text-gray-700">
                  Start Date <span style="color: red">*</span>
                </label>
                <input
                  v-model="form.startDate"
                  type="date"
                  required
                  class="w-full rounded bg-gray-100 p-2 mt-1 outline-none"
                />
                <p v-if="errors.startDate" class="text-red-600 mt-1 text-sm">
                  {{ errors.startDate[0] }}
                </p>
              </div>

              <div>
                <label class="text-sm font-medium text-gray-700">
                  End Date <span style="color: red">*</span>
                </label>
                <input
                  v-model="form.endDate"
                  type="date"
                  required
                  class="w-full rounded bg-gray-100 p-2 mt-1 outline-none"
                />
                <p v-if="errors.endDate" class="text-red-600 mt-1 text-sm">
                  {{ errors.endDate[0] }}
                </p>
              </div>

              <div>
                <label class="text-sm font-medium text-gray-700"
                  >Company Logo <span style="color: red">*</span></label
                >
                <input
                  type="file"
                  required
                  @change="handleLogoUpload"
                  class="w-full rounded bg-gray-100 p-2 mt-1 outline-none file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                />
                <p v-if="errors.logo" class="text-red-600 mt-1 text-sm">
                  {{ errors.logo[0] }}
                </p>
              </div>

              <div class="md:col-span-2 mt-4 border-t pt-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">
                  Target Audience
                </h3>
              </div>
              <div>
                <label class="text-sm font-medium text-gray-700"
                  >Target Location(s) <span style="color: red">*</span></label
                >
                <input
                  v-model="form.locations"
                  placeholder="e.g.Nairobi,Mombasa,Kenya"
                  type="text"
                  required
                  class="w-full rounded bg-gray-100 p-2 mt-1 outline-none"
                />
                <p v-if="errors.locations" class="text-red-600 mt-1 text-sm">
                  {{ errors.locations[0] }}
                </p>
              </div>
              <div>
                <label class="text-sm font-medium text-gray-700"
                  >Target Age Range <span style="color: red">*</span></label
                >
                <input
                  v-model="form.targetAge"
                  required
                  placeholder="e.g., 18-35"
                  type="text"
                  class="w-full rounded bg-gray-100 p-2 mt-1 outline-none"
                />
                <p v-if="errors.targetAge" class="text-red-600 mt-1 text-sm">
                  {{ errors.targetAge[0] }}
                </p>
              </div>
              <div>
                <label class="text-sm font-medium text-gray-700"
                  >Target Audience Interests
                  <span style="color: red">*</span></label
                >
                <input
                  required
                  v-model="form.targetInterests"
                  placeholder="e.g., Skincare, Fashion, Tech"
                  type="text"
                  class="w-full rounded bg-gray-100 p-2 mt-1 outline-none"
                />
                <p class="text-xs text-gray-500 mt-1">
                  Separate interests with a comma.
                </p>
                <p
                  v-if="errors.targetInterests"
                  class="text-red-600 mt-1 text-sm"
                >
                  {{ errors.targetInterests[0] }}
                </p>
              </div>
              <div>
                <label class="text-sm font-medium text-gray-700"
                  >Preffered Influencer Residence
                  <span style="color: red">*</span></label
                >
                <Multiselect
                  v-model="form.residence"
                  :options="counties"
                  style="height: 40px"
                  mode="tags"
                  class="w-full rounded bg-gray-100 p-2 mt-1 outline-none"
                />
              </div>
              <div class="md:col-span-2 mt-4 border-t pt-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">
                  Campaign Goals & KPIs
                </h3>
              </div>
              <div>
                <label class="text-sm font-medium text-gray-700"
                  >Primary Campaign Goal
                  <span style="color: red">*</span></label
                >
                <select
                  required
                  v-model="form.primaryGoal"
                  class="w-full rounded bg-gray-100 p-2 mt-1 outline-none"
                >
                  <option value="">Select Primary Goal</option>
                  <option>Brand Awareness</option>
                  <option>Lead Generation</option>
                  <option>Sales</option>
                  <option>App Installs</option>
                  <option>Engagement</option>
                  <option>User-Generated Content (UGC)</option>
                </select>
                <p v-if="errors.primaryGoal" class="text-red-600 mt-1 text-sm">
                  {{ errors.primaryGoal[0] }}
                </p>
              </div>
              <div>
                <label class="text-sm font-medium text-gray-700"
                  >Key Performance Indicators (KPIs)
                  <span style="color: red">*</span></label
                >
                <input
                  required
                  v-model="form.kpis"
                  placeholder="e.g., Reach, Clicks, Conversions"
                  type="text"
                  class="w-full rounded bg-gray-100 p-2 mt-1 outline-none"
                />
                <p v-if="errors.kpis" class="text-red-600 mt-1 text-sm">
                  {{ errors.kpis[0] }}
                </p>
              </div>

              <div class="md:col-span-2 mt-4 border-t pt-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">
                  Deliverables & Platforms
                </h3>
              </div>
              <div>
                <label class="text-sm font-medium text-gray-700"
                  >Platform(s) <span style="color: red">*</span></label
                >
                <div class="grid grid-cols-2 gap-2 mt-2">
                  <label
                    v-for="option in platformOptions"
                    :key="option"
                    class="flex items-center"
                  >
                    <input
                      id="platform{{ option }}"
                      type="checkbox"
                      :value="option"
                      v-model="form.platforms"
                      class="mr-2"
                    />
                    {{ option }}
                  </label>
                </div>
                <p v-if="errors.platforms" class="text-red-600 mt-1 text-sm">
                  {{ errors.platforms[0] }}
                </p>
              </div>

              <div>
                <label class="text-sm font-medium text-gray-700"
                  >Content Format(s) <span style="color: red">*</span></label
                >
                <input
                  v-model="form.contentFormats"
                  placeholder="e.g., Instagram Post, Facebook Post,Tiktok Video"
                  type="text"
                  required
                  class="w-full rounded bg-gray-100 p-2 mt-1 outline-none"
                />
                <p
                  v-if="errors.contentFormats"
                  class="text-red-600 mt-1 text-sm"
                >
                  {{ errors.contentFormats[0] }}
                </p>
              </div>
              <div class="md:col-span-2">
                <label class="text-sm font-medium text-gray-700"
                  >Required Hashtags & Mentions
                </label>
                <input
                  v-model="form.hashtags"
                  placeholder="#YourBrand @YourBrand"
                  type="text"
                  class="w-full rounded bg-gray-100 p-2 mt-1 outline-none"
                />
                <p v-if="errors.hashtags" class="text-red-600 mt-1 text-sm">
                  {{ errors.hashtags[0] }}
                </p>
              </div>

              <div class="md:col-span-2 mt-4 border-t pt-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">
                  Compensation
                </h3>
              </div>
              <div>
                <label class="text-sm font-medium text-gray-700"
                  >Compensation Type <span style="color: red">*</span></label
                >
                <select
                  required
                  v-model="form.compensationType"
                  class="w-full rounded bg-gray-100 p-2 mt-1 outline-none"
                >
                  <option>Fixed Rate</option>
                  <option>Product Gifting</option>
                  <option>Commission</option>
                  <option>Combination</option>
                </select>
                <p
                  v-if="errors.compensationType"
                  class="text-red-600 mt-1 text-sm"
                >
                  {{ errors.compensationType[0] }}
                </p>
              </div>
              <div>
                <label class="text-sm font-medium text-gray-700"
                  >Budget/Rate <span style="color: red">*</span></label
                >
                <input
                  v-model="form.budget"
                  required
                  type="text"
                  placeholder="e.g., $500 or 10% commission"
                  class="w-full rounded bg-gray-100 p-2 mt-1 outline-none"
                />
                <p v-if="errors.budget" class="text-red-600 mt-1 text-sm">
                  {{ errors.budget[0] }}
                </p>
              </div>
            </div>

            <div class="mt-6 border-t pt-4">
              <label class="text-sm font-medium text-gray-700"
                >Campaign Brief & Deliverable Details
                <span style="color: red">*</span></label
              >
              <p class="text-xs text-gray-500 mb-2">
                Provide a detailed description of the campaign, deliverables,
                talking points, and CTAs.
              </p>
              <QuillEditor
                v-model:content="form.brief"
                contentType="html"
                theme="snow"
                class="min-h-[150px]"
              />
              <p v-if="errors.brief" class="text-red-600 mt-1 text-sm">
                {{ errors.brief[0] }}
              </p>
            </div>
            <div class="mt-4">
              <label class="text-sm font-medium text-gray-700"
                >Creative Guidance (Dos and Don'ts)
                <span style="color: red">*</span></label
              >
              <p class="text-xs text-gray-500 mb-2">
                Outline any creative requirements, brand guidelines, or things
                to avoid.
              </p>
              <QuillEditor
                v-model:content="form.guidance"
                contentType="html"
                theme="snow"
                class="min-h-[150px]"
              />
              <p v-if="errors.guidance" class="text-red-600 mt-1 text-sm">
                {{ errors.guidance[0] }}
              </p>
            </div>
            <div class="mt-4">
              <label class="text-sm font-medium text-gray-700"
                >Influencer Eligibility Requirements
                <span style="color: red">*</span></label
              >
              <p class="text-xs text-gray-500 mb-2">
                Describe the ideal influencer for this campaign. (e.g., follower
                count, niche, engagement rate).
              </p>
              <QuillEditor
                v-model:content="form.eligibility"
                contentType="html"
                theme="snow"
                class="min-h-[150px]"
              />
              <p v-if="errors.eligibility" class="text-red-600 mt-1 text-sm">
                {{ errors.eligibility[0] }}
              </p>
            </div>

            <div class="mt-6 border-t pt-4">
              <h2 class="text-lg font-semibold text-gray-700 mb-2">
                Campaign Assets & Mood Board
              </h2>
              <div
                class="border-2 border-dashed border-gray-300 p-4 rounded-md text-center"
              >
                <label for="assets" class="text-sm text-gray-500"
                  >Drag & drop files here or click to upload (e.g., product
                  images, brand guide)</label
                >
                <input
                  ref="assets"
                  type="file"
                  id="assets"
                  multiple
                  class="hidden"
                  @change="handleAssetUpload"
                />
                <div class="mt-2">
                  <button
                    @click="$refs.assets.click()"
                    class="text-blue-600 underline"
                  >
                    Select Files
                  </button>
                </div>
              </div>
              <p v-if="errors.assets" class="text-red-600 mt-1 text-sm">
                {{ errors.assets[0] }}
              </p>
            </div>
          </div>

          <div class="bg-gray-200 px-4 py-3 text-right">
            <button
              @click="showModal = false"
              class="py-2 px-4 bg-gray-500 text-white rounded hover:bg-gray-700 mr-2"
            >
              Cancel
            </button>
            <button
              @click="submitForm"
              class="py-2 px-4 bg-blue-500 text-white rounded font-medium hover:bg-blue-700 transition duration-500"
            >
              Create Campaign
            </button>
          </div>
        </div>
      </div>
    </div>
  </template>
    <!-- Campaign Table -->
   