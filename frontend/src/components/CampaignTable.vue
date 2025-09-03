<template>
  <div class="grid grid-cols-1 gap-6 mb-6">
    <!-- Add Campaign Button -->

    <div
      v-if="showEditModal"
      class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50"
      id="editModal"
    >
      <div>
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
                  <label class="text-sm font-bold text-gray-700"
                    >Campaign Title
                    <span style="color: red">(Required)</span></label
                  >
                  <input
                    v-model="selectedCampaign.id"
                    type="hidden"
                    required
                    placeholder="e.g., Summer Skincare Launch"
                    class="w-full rounded bg-gray-100 p-2 mt-1 outline-none"
                  />
                  <input
                    v-model="selectedCampaign.title"
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
                  <label class="text-sm font-bold text-gray-700"
                    >Brand Name
                    <span style="color: red">(Required)</span></label
                  >
                  <input
                    v-model="selectedCampaign.brand"
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
                  <label class="text-sm font-bold text-gray-700"
                    >Company Website
                    <span style="color: #56be78">(Optional)</span></label
                  >
                  <input
                    v-model="selectedCampaign.website"
                    type="url"
                    placeholder="https://www.yourbrand.com"
                    class="w-full rounded bg-gray-100 p-2 mt-1 outline-none"
                  />
                  <p v-if="errors.website" class="text-red-600 mt-1 text-sm">
                    {{ errors.website[0] }}
                  </p>
                </div>
                <div class="md:col-span-2">
                  <label class="text-sm font-bold text-gray-700"
                    >Campaign Description
                    <span style="color: red">(Required)</span></label
                  >
                  <textarea
                    v-model="selectedCampaign.about"
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
                  <label class="text-sm font-bold text-gray-700">
                    Start Date <span style="color: red">(Required)</span>
                  </label>
                  <input
                    v-model="selectedCampaign.start_date"
                    type="date"
                    required
                    class="w-full rounded bg-gray-100 p-2 mt-1 outline-none"
                  />
                  <p v-if="errors.startDate" class="text-red-600 mt-1 text-sm">
                    {{ errors.startDate[0] }}
                  </p>
                </div>

                <div>
                  <label class="text-sm font-bold text-gray-700">
                    End Date <span style="color: red">(Required)</span>
                  </label>
                  <input
                    v-model="selectedCampaign.end_date"
                    type="date"
                    required
                    class="w-full rounded bg-gray-100 p-2 mt-1 outline-none"
                  />
                  <p v-if="errors.endDate" class="text-red-600 mt-1 text-sm">
                    {{ errors.endDate[0] }}
                  </p>
                </div>

                <div>
                  <label class="text-sm font-bold text-gray-700"
                    >Target Age Range
                    <span style="color: red">(Required)</span></label
                  >
                  <input
                    v-model="selectedCampaign.target_age"
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
                  <label class="text-sm font-bold text-gray-700"
                    >Target Audience Interests
                    <span style="color: red">(Required)</span></label
                  >
                  <input
                    required
                    v-model="selectedCampaign.target_interests"
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
                    >Preffered Locations
                    <span style="color: red">(Required)</span></label
                  >

                  <Multiselect
                    v-model="selectedCampaign.residence"
                    :options="counties"
                    style="height: 40px"
                    mode="tags"
                    class="w-full rounded bg-gray-100 p-2 mt-1 outline-none"
                  />
                </div>
                <!-- <div class="md:col-span-2 mt-4 border-t pt-4">
                  <h3 class="text-lg font-semibold text-gray-800 mb-2">
                    Campaign Goals & KPIs
                  </h3>
                </div> -->
                <div>
                  <label class="text-sm font-bold text-gray-700"
                    >Primary Campaign Goal
                    <span style="color: red">(Required)</span></label
                  >
                  <select
                    required
                    v-model="selectedCampaign.primary_goal"
                    class="w-full rounded bg-gray-100 p-2 mt-1 outline-none"
                  >
                    <option value="">Select Primary Goal</option>
                    <option>Brand Awareness</option>
                    <option>Lead Generation</option>
                    <option>Sales</option>
                    <option>App Installs</option>
                    <option>Website Traffic</option>
                    <option>Engagement</option>
                    <option>User-Generated Content (UGC)</option>
                    <option>Product Launch</option>
                    <option>Event Promotion</option>
                    <option>Influencer Collaboration</option>
                    <option>Customer Retention</option>
                    <option>Rebranding</option>
                    <option>Market Research</option>
                    <option>Community Building</option>
                    <option>New Store Opening</option>
                    <option>Drive In-Store Visits</option>
                  </select>
                  <p
                    v-if="errors.primaryGoal"
                    class="text-red-600 mt-1 text-sm"
                  >
                    {{ errors.primaryGoal[0] }}
                  </p>
                </div>

                <div>
                  <label class="text-sm font-bold text-gray-700"
                    >Platform(s)
                    <span style="color: red">(Required)</span></label
                  >
                  <div class="grid grid-cols-2 gap-2 mt-2">
                    <label
                      v-for="option in platformOptions"
                      :key="option"
                      class="flex items-center"
                    >
                      <input
                        :id="`platform${option}`"
                        type="checkbox"
                        :value="option"
                        v-model="selectedCampaign.platforms"
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
                  <label class="text-sm font-bold text-gray-700"
                    >Content Format(s)
                    <span style="color: red">(Required)</span></label
                  >
                  <input
                    v-model="selectedCampaign.content_formats"
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
                  <label class="text-sm font-bold text-gray-700"
                    >Compensation Budget/Rate
                    <span style="color: red">(Required)</span></label
                  >
                  <input
                    v-model="selectedCampaign.budget"
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
                <label class="text-sm font-bold text-gray-700"
                  >Campaign Brief & Deliverable Details
                  <span style="color: red">(Required)</span></label
                >
                <p class="text-xs text-gray-500 mb-2">
                  Provide a detailed description of the campaign, deliverables,
                  talking points, and CTAs.
                </p>
                <QuillEditor
                  v-model:content="selectedCampaign.brief"
                  contentType="html"
                  theme="snow"
                  class="min-h-[150px]"
                />
                <p v-if="errors.brief" class="text-red-600 mt-1 text-sm">
                  {{ errors.brief[0] }}
                </p>
              </div>
              <div class="mt-4">
                <label class="text-sm font-bold text-gray-700"
                  >Creative Guidance (Dos and Don'ts)
                  <span style="color: red">(Required)</span></label
                >
                <p class="text-xs text-gray-500 mb-2">
                  Outline any creative requirements, brand guidelines, or things
                  to avoid.
                </p>
                <QuillEditor
                  v-model:content="selectedCampaign.guidance"
                  contentType="html"
                  theme="snow"
                  class="min-h-[150px]"
                />
                <p v-if="errors.guidance" class="text-red-600 mt-1 text-sm">
                  {{ errors.guidance[0] }}
                </p>
              </div>
              <div class="mt-4">
                <label class="text-sm font-bold text-gray-700"
                  >Influencer Eligibility Requirements
                  <span style="color: red">(Required)</span></label
                >
                <p class="text-xs text-gray-500 mb-2">
                  Describe the ideal influencer for this campaign. (e.g.,
                  follower count, niche, engagement rate).
                </p>
                <QuillEditor
                  v-model:content="selectedCampaign.eligibility"
                  contentType="html"
                  theme="snow"
                  class="min-h-[150px]"
                />
                <p v-if="errors.eligibility" class="text-red-600 mt-1 text-sm">
                  {{ errors.eligibility[0] }}
                </p>
              </div>

              <div class="mt-6 border-t pt-4">
                <h2 class="text-lg font-bold text-gray-700 mb-2">
                  Campaign Assets <span style="color: red">(Required)</span>
                </h2>

                <!-- File upload container -->
                <div
                  class="border-2 border-dashed border-gray-300 p-4 rounded-md text-center"
                >
                  <label for="assets" class="text-sm text-gray-500">
                    Drag & drop files here or click to upload (e.g., product
                    images, brand guide)
                  </label>
                  <input
                    ref="assets"
                    type="file"
                    id="assets"
                    multiple
                    class="hidden"
                    @change="handleUpdateAssetUpload"
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

                <!-- PREVIEW SECTION -->
                <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4">
                  <!-- Existing Assets (URLs) -->
                  <div
                    v-for="(asset, index) in existingAssets"
                    :key="'existing-' + index"
                    class="relative"
                  >
                    <img
                      :src="asset"
                      class="w-full h-32 object-cover rounded-md"
                    />
                    <button
                      @click="removeExistingAsset(index)"
                      class="absolute top-1 right-1 bg-red-600 text-white text-xs px-2 py-1 rounded-full"
                    >
                      X
                    </button>
                  </div>

                  <!-- New Assets (Files) -->
                  <div
                    v-for="(file, index) in newAssets"
                    :key="'new-' + index"
                    class="relative"
                  >
                    <img
                      :src="file.preview"
                      class="w-full h-32 object-cover rounded-md"
                    />
                    <button
                      @click="removeNewAsset(index)"
                      class="absolute top-1 right-1 bg-red-600 text-white text-xs px-2 py-1 rounded-full"
                    >
                      X
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
                @click="closeEditModal"
                class="py-2 px-4 bg-gray-500 text-white rounded hover:bg-gray-700 mr-2"
              >
                Cancel
              </button>
              <button
                @click="updateCampaign"
                style="background-color: #e11d50"
                class="py-2 px-4 text-white rounded font-medium transition duration-500"
              >
                Update Campaign
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

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
                <label class="text-sm font-bold text-gray-700"
                  >Campaign Title
                  <span style="color: red">(Required)</span></label
                >
                <input
                  v-model="form.title"
                  type="text"
                  required
                  minlength="10"
                  maxlength="60"
                  placeholder="e.g. Aloe Skincare Awareness Drive"
                  class="w-full rounded bg-gray-100 p-2 mt-1 outline-none"
                />

                <p v-if="errors.title" class="text-red-600 mt-1 text-sm">
                  {{ errors.title[0] }}
                </p>
              </div>
              <div>
                <label class="text-sm font-bold text-gray-700"
                  >Brand Name <span style="color: red">(Required)</span></label
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
                <label class="text-sm font-bold text-gray-700"
                  >Company Website
                  <span style="color: #56be78">(Optional)</span></label
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
                <label class="text-sm font-bold text-gray-700"
                  >Campaign Description
                  <span style="color: red">(Required)</span></label
                >
                <textarea
                  v-model="form.about"
                  rows="3"
                  required
                  placeholder="The Aloe Skincare Awareness Drive campaign is aimed at driving awareness and sales during the slow mid-month period by engaging influencers to promote Aloe Skincare as the go-to affordable, skin care choice for families."
                  class="w-full rounded bg-gray-100 p-2 mt-1 outline-none"
                ></textarea>
                <p v-if="errors.about" class="text-red-600 mt-1 text-sm">
                  {{ errors.about[0] }}
                </p>
              </div>
              <div>
                <label class="text-sm font-bold text-gray-700">
                  Campaign Start Date <span style="color: red">(Required)</span>
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
                <label class="text-sm font-bold text-gray-700">
                  Campaign End Date <span style="color: red">(Required)</span>
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
              <!-- <div class="md:col-span-2 mt-4 border-t pt-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">
                  Target Audience
                </h3>
              </div> -->
              <!-- <div>
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
              </div> -->
              <div>
                <label class="text-sm font-bold text-gray-700"
                  >Target Age Range
                  <span style="color: red">(Required)</span></label
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
                <label class="text-sm font-bold text-gray-700"
                  >Target Audience Interests
                  <span style="color: red">(Required)</span></label
                >
                <input
                  required
                  v-model="form.targetInterests"
                  placeholder="e.g.,Students, Skincare, Fashion, Tech"
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
                <label class="text-sm font-bold text-gray-700"
                  >Preffered Location
                  <span style="color: red">(Required)</span></label
                >
                <Multiselect
                  v-model="form.residence"
                  :options="counties"
                  style="height: 40px"
                  mode="tags"
                  class="w-full rounded bg-gray-100 p-2 mt-1 outline-none"
                />
              </div>
              <!-- <div class="md:col-span-2 mt-4 border-t pt-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">
                  Campaign Goals & KPIs
                </h3>
              </div> -->
              <div>
                <label class="text-sm font-bold text-gray-700"
                  >Primary Campaign Goal
                  <span style="color: red">(Required)</span></label
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
                  <option>Website Traffic</option>
                  <option>Engagement</option>
                  <option>User-Generated Content (UGC)</option>
                  <option>Product Launch</option>
                  <option>Event Promotion</option>
                  <option>Influencer Collaboration</option>
                  <option>Customer Retention</option>
                  <option>Rebranding</option>
                  <option>Market Research</option>
                  <option>Community Building</option>
                  <option>New Store Opening</option>
                  <option>Drive In-Store Visits</option>
                </select>
                <p v-if="errors.primaryGoal" class="text-red-600 mt-1 text-sm">
                  {{ errors.primaryGoal[0] }}
                </p>
              </div>
              <!-- <div>
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
              </div> -->

              <!-- <div class="md:col-span-2 mt-4 border-t pt-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">
                  Deliverables & Platforms
                </h3>
              </div> -->
              <div>
                <label class="text-sm font-bold text-gray-700"
                  >Preferred Channels
                  <span style="color: red">(Required)</span></label
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
                <label class="text-sm font-bold text-gray-700"
                  >Content Format(s)
                  <span style="color: red">(Required)</span></label
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
              <!-- <div class="md:col-span-2">
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
              </div> -->

              <!-- <div class="md:col-span-2 mt-4 border-t pt-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">
                  Compensation
                </h3>
              </div> -->
              <!-- <div>
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
              </div> -->
              <div class="md:col-span-2">
                <label class="text-sm font-bold text-gray-700"
                  >Compensation Budget/Rate
                  <span style="color: red">(Required)</span></label
                >
                <input
                  v-model="form.budget"
                  required
                  type="text"
                  placeholder="e.g., KES 500 or 10% commission or Product Gifting"
                  class="w-full rounded bg-gray-100 p-2 mt-1 outline-none"
                />
                <p v-if="errors.budget" class="text-red-600 mt-1 text-sm">
                  {{ errors.budget[0] }}
                </p>
              </div>
            </div>

            <div class="mt-6 border-t pt-4">
              <label class="text-sm font-bold text-gray-700"
                >Campaign Brief & Deliverable Guideline
                <span style="color: red">(Required)</span></label
              >
              <p class="text-xs text-gray-500 mb-2">
                Please provide what influencers are expected to do, key talking
                points to focus on, and the call-to-action (CTA) you want
                included in their content.
              </p>
              <QuillEditor
                v-model:content="form.brief"
                contentType="html"
                theme="snow"
                placeholder="e.g. Influencers will showcase their daily skincare routine using Aloe Skincare products, highlighting the hydrating and soothing effects of aloe vera. Deliverables include one Instagram Reel or TikTok video, and one Instagram Story. Key talking points: natural ingredients, skin healing, and everyday glow. CTA: 'Try the Aloe Glow today!'"
                class="min-h-[150px]"
              />
              <p v-if="errors.brief" class="text-red-600 mt-1 text-sm">
                {{ errors.brief[0] }}
              </p>
            </div>
            <div class="mt-4">
              <label class="text-sm font-bold text-gray-700"
                >Creative Guidance (Dos and Don’ts)
                <span style="color: red">(Required)</span></label
              >
              <p class="text-xs text-gray-500 mb-2">
                Please follow these brand-aligned guidelines to maintain
                consistency and authenticity in the campaign content.
              </p>
              <QuillEditor
                v-model:content="form.guidance"
                contentType="html"
                theme="snow"
                placeholder="e.g. 
✅ DOs:
- Showcase your skincare routine using Aloe Skincare products.
- Highlight benefits like hydration, soothing, and natural glow.
🚫 DON'Ts:
- Don’t make medical claims (e.g., 'cures acne')."
                class="min-h-[150px]"
              />
              <p v-if="errors.guidance" class="text-red-600 mt-1 text-sm">
                {{ errors.guidance[0] }}
              </p>
            </div>
            <div class="mt-4">
              <label class="text-sm font-bold text-gray-700"
                >Influencer Eligibility Requirements
                <span style="color: red">(Required)</span></label
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
                placeholder="e.g. Ideal influencers for this campaign should have:
- A minimum of 2,000 followers on Instagram or TikTok.
- A strong presence in skincare, beauty, wellness, or lifestyle niches.
- An engagement rate of at least 3%.
- A primarily Kenyan audience aged 18–35.
- Experience with skincare product reviews or routines is a plus.
- Authentic content style and a strong personal brand."
              />

              <p v-if="errors.eligibility" class="text-red-600 mt-1 text-sm">
                {{ errors.eligibility[0] }}
              </p>
            </div>

            <div class="mt-6 border-t pt-4">
              <h2 class="text-lg font-bold text-gray-700 mb-2">
                Campaign Assets<span style="color: red">(Required)</span>
              </h2>
              <div
                class="border-2 border-dashed border-gray-300 p-4 rounded-md text-center"
              >
                <label for="assets" class="text-sm text-gray-500"
                  >Drag & drop files here or click to upload (e.g., product
                  images)</label
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
              style="background-color: #fd624e"
              class="py-2 px-4 bg-blue-500 text-white rounded font-medium hover:bg-blue-700 transition duration-500"
            >
              Create Campaign
            </button>
          </div>
        </div>
      </div>
    </div>
    <!-- Campaign Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <div
        class="px-6 py-4 border-b border-gray-200 flex justify-between items-center"
      >
        <h2 class="text-lg font-semibold text-gray-800">Recent Campaigns</h2>

        <div>
          <button
            v-if="auth.user?.limit > 0"
            class="py-2 px-6 text-white rounded font-medium transition duration-500"
            :style="{ background: '#fd624e' }"
            @click="showModal = true"
          >
            <font-awesome-icon :icon="['fas', 'plus']" /> Add Campaign
            <!-- {{ auth.user?.limit }} -->
          </button>

          <div
            v-else
            class="text-sm text-yellow-700 bg-yellow-100 border border-yellow-400 rounded px-4 py-2"
          >
            Campaign limit reached.
            <router-link to="/upgrade" class="text-pink-600 underline"
              >Upgrade your plan</router-link
            >
            to add more.
          </div>
        </div>
      </div>

      <div class="overflow-x-auto">
        <div class="p-4">
          <input
            v-model="search"
            placeholder="Search campaigns..."
            class="p-2 border rounded"
          />
        </div>

        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th
                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase"
              >
                #
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase"
              >
                Title
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase"
              >
                Brand
              </th>
              <!-- <th
        class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase"
      >
        Location
      </th> -->
              <th
                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase"
              >
                Compensation
              </th>
              <!-- <th
        class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase"
      >
        Budget
      </th> -->
              <th
                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase"
              >
                Start
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase"
              >
                End
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase"
              >
                Status
              </th>
              <th
                class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase"
              >
                Actions
              </th>
            </tr>
          </thead>

          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="(campaign, index) in paginatedCampaigns" :key="index">
              <td class="px-6 py-4 whitespace-nowrap">
                {{ (currentPage - 1) * perPage + index + 1 }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap" style="color: dodgerblue">
                <router-link :to="`/campaigns/${encodeId(campaign.id)}`">
                  {{ campaign.title }}
                </router-link>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                {{ campaign.brand }}
              </td>
              <!-- <td class="px-6 py-4 whitespace-nowrap">
        {{ campaign.locations }}
      </td> -->
              <td class="px-6 py-4 whitespace-nowrap">
                {{ campaign.budget }}
              </td>
              <!-- <td class="px-6 py-4 whitespace-nowrap">
        {{ campaign.budget }}
      </td> -->
              <td class="px-6 py-4 whitespace-nowrap">
                {{ new Date(campaign.start_date).toLocaleDateString() }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                {{ new Date(campaign.end_date).toLocaleDateString() }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  :class="[
                    'px-2 py-1 rounded text-xs font-semibold',
                    campaign.status == 1
                      ? 'bg-green-100 text-green-800'
                      : campaign.status == 0
                      ? 'bg-red-100 text-red-800'
                      : 'bg-yellow-100 text-yellow-800',
                  ]"
                >
                  {{
                    campaign.status == 1
                      ? "Active"
                      : campaign.status == 0
                      ? "Inactive"
                      : "Pending"
                  }}
                </span>
              </td>

              <td
                class="px-6 py-4 whitespace-nowrap text-center flex justify-center gap-2"
              >
                <button
                  @click="editCampaign(campaign)"
                  class="px-2 py-1 mr-2 bg-blue-500 text-gray-100 rounded text-xs flex items-center gap-1"
                >
                  <Pencil :size="14" />
                  Edit
                </button>
                <button
                  @click="toggleCampaignStatus(campaign)"
                  :disabled="campaign.status == 3"
                  :class="[
                    'px-2 py-1 text-gray-100 rounded text-xs flex items-center gap-1',
                    campaign.status == 1
                      ? 'bg-red-500'
                      : campaign.status == 0
                      ? 'bg-green-500'
                      : 'bg-gray-400 cursor-not-allowed',
                  ]"
                >
                  <template v-if="campaign.status == 1">
                    <X :size="14" />
                    Deactivate
                  </template>
                  <template v-else-if="campaign.status == 0">
                    <Check :size="14" />
                    Activate
                  </template>
                  <template v-else>
                    <X :size="14" />
                    Pending
                  </template>
                </button>
              </td>
            </tr>
          </tbody>
        </table>

        <div class="flex justify-between p-4">
          <button @click="prevPage" :disabled="currentPage <= 1">
            Previous
          </button>
          <span>{{ currentPage }} / {{ totalPages }}</span>
          <button @click="nextPage" :disabled="currentPage >= totalPages">
            Next
          </button>
        </div>
      </div>
    </div>
  </div>
</template>


<script setup>
// Vue and core imports
import { ref, watch, onMounted, computed } from "vue";
import { QuillEditor } from "@vueup/vue-quill";
import "@vueup/vue-quill/dist/vue-quill.snow.css";
import campaignService from "@/services/campaignService";
import { useToast } from "vue-toast-notification";
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
import { useAuthStore } from "@/stores/auth";
import { encodeId, truncateWords,toYMD } from "@/utils/helpers";

const auth = useAuthStore();
const $toast = useToast();

// Reactive States
const campaigns = ref([]);
const showModal = ref(false);
const showEditModal = ref(false);
const selectedCampaign = ref(null);
const search = ref("");
const currentPage = ref(1);
const perPage = ref(10);
const existingAssets = ref([]);
const newAssets = ref([]);

const today = new Date();
const sevenDaysLater = new Date();
sevenDaysLater.setDate(today.getDate() + 7);

const form = ref({
  title: "",
  brand: "",
  website: "",
  about: "",
  startDate: toYMD(today),
  endDate: toYMD(sevenDaysLater),
  locations: "",
  targetAge: "",
  targetInterests: "",
  primaryGoal: "",
  kpis: "",
  platforms: [],
  contentFormats: "",
  hashtags: "",
  compensationType: "Fixed Rate",
  budget: "",
  brief: "",
  guidance: "",
  eligibility: "",
  residence: ["Any County"],
  assets: [],
});

const errors = ref({});
const baseUrl = "https://demo.ngumzo.com/storage/";
const counties = [
  "Any County",
  "Baringo",
  "Bomet",
  "Bungoma",
  "Busia",
  "Elgeyo-Marakwet",
  "Embu",
  "Garissa",
  "Homa Bay",
  "Isiolo",
  "Kajiado",
  "Kakamega",
  "Kericho",
  "Kiambu",
  "Kilifi",
  "Kirinyaga",
  "Kisii",
  "Kisumu",
  "Kitui",
  "Kwale",
  "Laikipia",
  "Lamu",
  "Machakos",
  "Makueni",
  "Mandera",
  "Marsabit",
  "Meru",
  "Migori",
  "Mombasa",
  "Murang'a",
  "Nairobi",
  "Nakuru",
  "Nandi",
  "Narok",
  "Nyamira",
  "Nyandarua",
  "Nyeri",
  "Samburu",
  "Siaya",
  "Taita-Taveta",
  "Tana River",
  "Tharaka-Nithi",
  "Trans Nzoia",
  "Turkana",
  "Uasin Gishu",
  "Vihiga",
  "Wajir",
  "West Pokot",
];

const platformOptions = [
  "Instagram",
  "TikTok",
  "YouTube",
  "Facebook",
  "Blog",
  "Twitter (X)",
  "Field Activation",
];



const fetchCampaigns = async () => {
  try {
    const { data } = await campaignService.get();
    campaigns.value = data.campaigns;
  } catch (error) {
    console.error("Error fetching campaigns.", error);
  }
};
function closeEditModal() {
  const modal = document.getElementById("editModal");
  modal.classList.remove("flex");
  modal.classList.add("hidden");
}
const handleLogoUpload = (event) => {
  form.value.logo = event.target.files[0];
};

const handleAssetUpload = (event) => {
  form.value.assets = Array.from(event.target.files);
};

const submitForm = async () => {
  errors.value = {};
  const formData = new FormData();
  formData.append("user_id", auth.user?.id);
  for (const key in form.value) {
    const value = form.value[key];
    if (key === "assets" && value.length > 0) {
      value.forEach((file) => formData.append("assets[]", file));
    } else if (key === "platforms" && value.length > 0) {
      value.forEach((platform) => formData.append("platforms[]", platform));
    } else if (value !== null && key !== "assets" && key !== "platforms") {
      formData.append(key, value);
    }
  }

  try {
    await campaignService.post(formData);
    Object.keys(form.value).forEach((key) => {
      form.value[key] = key === "residence" ? ["Any County"] : "";
    });
    $toast.success("Campaign created successfully!", { position: "top-right" });
    showModal.value = false;
    await fetchCampaigns();
    await auth.refreshUser();
  } catch (error) {
    if (error.response?.status === 422) {
      errors.value = error.response.data.errors;
    } else {
      alert("An error occurred. Please try again.");
    }
  }
};

function editCampaign(campaign) {
  showEditModal.value = false;
  selectedCampaign.value = null;

  setTimeout(() => {
    selectedCampaign.value = {
      ...campaign,
      start_date: campaign.start_date?.split("T")[0],
      end_date: campaign.end_date?.split("T")[0],
      residence: campaign.residence?.split(",").map((i) => i.trim()) || [],
      platforms: Array.isArray(campaign.platforms) ? campaign.platforms : [],
    };
    existingAssets.value = (campaign.assets || [])
      .filter((a) => typeof a === "string")
      .map((a) => (a.startsWith("http") ? a : baseUrl + a));
    newAssets.value = [];
    showEditModal.value = true;
  }, 50);
}

const handleUpdateAssetUpload = (event) => {
  const files = Array.from(event.target.files);
  files.forEach((file) => {
    file.preview = URL.createObjectURL(file);
    newAssets.value.push(file);
  });
  selectedCampaign.value.assets = [...existingAssets.value, ...newAssets.value];
};

const removeExistingAsset = (index) => {
  existingAssets.value.splice(index, 1);
  selectedCampaign.value.assets = [...existingAssets.value, ...newAssets.value];
};

const removeNewAsset = (index) => {
  URL.revokeObjectURL(newAssets.value[index].preview);
  newAssets.value.splice(index, 1);
  selectedCampaign.value.assets = [...existingAssets.value, ...newAssets.value];
};

const updateCampaign = async () => {
  errors.value = {};
  const formData = new FormData();
  if (selectedCampaign.value.logo instanceof File) {
    formData.append("logo", selectedCampaign.value.logo);
  }
  selectedCampaign.value.assets
    ?.filter((a) => a instanceof File)
    .forEach((file) => {
      formData.append("assets[]", file);
    });
  selectedCampaign.value.assets
    ?.filter((a) => !(a instanceof File))
    .forEach((url) => {
      formData.append("existing_assets[]", url);
    });
  selectedCampaign.value.platforms?.forEach((p) =>
    formData.append("platforms[]", p)
  );

  for (const key in selectedCampaign.value) {
    if (["logo", "assets", "platforms"].includes(key)) continue;
    formData.append(key, selectedCampaign.value[key]);
  }

  try {
    await campaignService.update(selectedCampaign.value.id, formData);
    $toast.success("Campaign updated successfully!");
    showEditModal.value = false;
    await fetchCampaigns();
  } catch (error) {
    const res = error.response;
    if (res?.status === 422 && res.data.errors) {
      errors.value = Object.entries(res.data.errors).reduce(
        (acc, [field, msgs]) => {
          acc[field.replace(/\.\d+$/, "")] = msgs;
          return acc;
        },
        {}
      );
      $toast.error(Object.values(errors.value)[0] || "Validation failed");
    } else {
      alert(res?.data?.message || "Something went wrong.");
    }
  }
};

const filteredCampaigns = computed(() => {
  if (!search.value) return campaigns.value;
  return campaigns.value.filter((c) =>
    [c.title, c.brand, c.locations].some((f) =>
      f?.toLowerCase().includes(search.value.toLowerCase())
    )
  );
});

const paginatedCampaigns = computed(() => {
  const start = (currentPage.value - 1) * perPage.value;
  return filteredCampaigns.value.slice(start, start + perPage.value);
});

const totalPages = computed(() =>
  Math.ceil(filteredCampaigns.value.length / perPage.value)
);
function nextPage() {
  if (currentPage.value < totalPages.value) currentPage.value++;
}
function prevPage() {
  if (currentPage.value > 1) currentPage.value--;
}

const selectAll = ref(false);
function toggleSelectAll() {
  form.value.residence = selectAll.value ? [...counties] : [];
}

watch(
  () => form.value.residence,
  (val) => {
    selectAll.value = val.length === counties.length;
  }
);

onMounted(async () => {
  await fetchCampaigns();
  await auth.refreshUser();
});
</script>


<style scoped>
body {
  overflow-x: hidden;
}
</style>
