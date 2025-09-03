<template>
  <div
    class="bg-gray-100 min-h-screen py-10 px-4 w-full"
    style="margin-top: -50px"
  >
    <div
      class="max-w-6xl mx-auto rounded-xl overflow-hidden border border-gray-200 shadow-lg transition-shadow hover:shadow-xl bg-white"
    >
      <!-- Card Header -->
      <div class="bg-[#64b4f9] py-5 px-6 rounded-t-xl border-b border-blue-300">
        <h2 class="text-xl font-semibold text-white">My Profile</h2>
      </div>

      <!-- Card Body -->
      <form
        @submit.prevent="updateProfile"
        class="flex flex-col md:flex-row gap-10 p-6"
      >
        <!-- Left: Profile Picture -->
        <div class="flex flex-col items-center md:w-1/3">
          <div class="relative">
            <img
              :src="preview || defaultAvatar"
              alt="Profile Picture"
              class="h-40 w-40 object-cover rounded-full border border-gray-300 shadow-md"
            />
            <input type="file" @change="handleFileChange" class="mt-4" />
          </div>
        </div>

        <!-- Right: Profile Details -->
        <div class="flex-1 space-y-6">
          <!-- Name -->
          <div>
            <label class="block text-gray-700 font-medium mb-1">Name</label>
            <input
              v-model="form.name"
              type="text"
              class="w-full border border-gray-300 rounded px-4 py-2 focus:ring-2 focus:ring-pink-500"
            />
          </div>
          <div v-if="auth.user?.role === 'promoter'">
            <!-- Bio -->
            <div>
              <label class="block text-gray-700 font-medium mb-1">Bio</label>
              <textarea
                v-model="form.bio"
                rows="4"
                placeholder="Tell us about yourself, your content style, and audience"
                class="w-full border border-gray-300 rounded px-4 py-2 focus:ring-2 focus:ring-pink-500"
              ></textarea>
            </div>
            <!-- Niche / Skills -->
            <div>
              <label class="block text-gray-700 font-medium mb-1"
                >Niche / Skills</label
              >
              <input
                v-model="form.niche"
                type="text"
                placeholder="E.g. Fashion, Tech Reviews, Cooking"
                class="w-full border border-gray-300 rounded px-4 py-2 focus:ring-2 focus:ring-pink-500"
              />
            </div>

            <!-- Portfolio Link -->
            <!-- Location -->
            <div>
              <label class="block text-gray-700 font-medium mb-1">County</label>
              <select
                v-model="form.location"
                class="w-full border border-gray-300 rounded px-4 py-2 focus:ring-2 focus:ring-pink-500"
              >
                <option value="">Select County</option>
                <option
                  v-for="county in kenyanCounties"
                  :key="county"
                  :value="county"
                >
                  {{ county }}
                </option>
              </select>
            </div>

            <!-- Date of Birth -->
            <div>
              <label class="block text-gray-700 font-medium mb-1">
                Date of Birth
              </label>
              <input
                v-model="form.dob"
                type="date"
                class="w-full border border-gray-300 rounded px-4 py-2 focus:ring-2 focus:ring-pink-500"
                :max="maxDob"
              />
            </div>
          </div>
          <!-- Email -->
          <div>
            <label class="block text-gray-700 font-medium mb-1">Email</label>
            <input
              readonly
              v-model="form.email"
              type="email"
              class="w-full border border-gray-300 rounded px-4 py-2 bg-gray-100 cursor-not-allowed"
            />
          </div>

          <!-- Phone -->
          <div>
            <label class="block text-gray-700 font-medium mb-1">Phone</label>
            <input
              v-model="form.phone"
              type="tel"
              class="w-full border border-gray-300 rounded px-4 py-2 focus:ring-2 focus:ring-pink-500"
            />
          </div>

          <!-- Social Media Links -->
          <div v-if="auth.user?.role === 'promoter'">
            <label class="block text-gray-700 font-medium mb-1">TikTok</label>
            <input
              v-model="form.tiktok"
              type="url"
              placeholder="TikTok profile link"
              class="w-full border border-gray-300 rounded px-4 py-2 mb-4 focus:ring-2 focus:ring-pink-500"
            />

            <label class="block text-gray-700 font-medium mb-1">Facebook</label>
            <input
              v-model="form.facebook"
              type="url"
              placeholder="Facebook profile link"
              class="w-full border border-gray-300 rounded px-4 py-2 mb-4 focus:ring-2 focus:ring-pink-500"
            />

            <label class="block text-gray-700 font-medium mb-1">Twitter</label>
            <input
              v-model="form.twitter"
              type="url"
              placeholder="Twitter profile link"
              class="w-full border border-gray-300 rounded px-4 py-2 mb-4 focus:ring-2 focus:ring-pink-500"
            />

            <label class="block text-gray-700 font-medium mb-1"
              >Instagram</label
            >
            <input
              v-model="form.instagram"
              type="url"
              placeholder="Instagram profile link"
              class="w-full border border-gray-300 rounded px-4 py-2 mb-4 focus:ring-2 focus:ring-pink-500"
            />

            <label class="block text-gray-700 font-medium mb-1">YouTube</label>
            <input
              v-model="form.linkedin"
              type="url"
              placeholder="Linkedin profile link"
              class="w-full border border-gray-300 rounded px-4 py-2 mb-4 focus:ring-2 focus:ring-pink-500"
            />
            <label class="block text-gray-700 font-medium mb-1">Linkedin</label>
            <input
              v-model="form.youtube"
              type="url"
              placeholder="YouTube profile link"
              class="w-full border border-gray-300 rounded px-4 py-2 mb-4 focus:ring-2 focus:ring-pink-500"
            />
          </div>

          <!-- Password Fields -->
          <div>
            <label class="block text-gray-700 font-medium mb-1"
              >Current Password</label
            >
            <input
              v-model="form.currentPassword"
              type="password"
              class="w-full border border-gray-300 rounded px-4 py-2 focus:ring-2 focus:ring-pink-500"
              placeholder="Required to make changes"
            />
          </div>

          <div>
            <label class="block text-gray-700 font-medium mb-1"
              >New Password</label
            >
            <input
              v-model="form.newPassword"
              type="password"
              class="w-full border border-gray-300 rounded px-4 py-2 focus:ring-2 focus:ring-pink-500"
              placeholder="Leave blank to keep current"
            />
          </div>

          <div>
            <label class="block text-gray-700 font-medium mb-1"
              >Confirm Password</label
            >
            <input
              v-model="form.confirmPassword"
              type="password"
              class="w-full border border-gray-300 rounded px-4 py-2 focus:ring-2 focus:ring-pink-500"
            />
          </div>

          <!-- Submit -->
          <div>
            <button
              type="submit"
              class="bg-pink-600 text-white px-6 py-2 rounded hover:bg-pink-700 transition"
            >
              Update Profile
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>
<script setup>
import { ref } from "vue";
import { useAuthStore } from "@/stores/auth";
import { useToast } from "vue-toast-notification";
import axios from "axios";

const auth = useAuthStore();
const $toast = useToast();

const defaultAvatar = auth.user?.photo
  ? `https://demo.ngumzo.com/storage/${auth.user.photo}`
  : "/profile.jpg";
const today = new Date();
const maxDob = ref(
  new Date(today.setFullYear(today.getFullYear() - 18))
    .toISOString()
    .split("T")[0]
);
const kenyanCounties = [
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

const form = ref({
  name: auth.user?.name || "",
  email: auth.user?.email || "",
  phone: auth.user?.phone || "",
  currentPassword: "",
  newPassword: "",
  confirmPassword: "",
  photo: null,
  tiktok: auth.user?.tiktok || "",
  facebook: auth.user?.facebook || "",
  twitter: auth.user?.twitter || "",
  instagram: auth.user?.instagram || "",
  linkedin: auth.user?.linkedin || "",
  youtube: auth.user?.youtube || "",
  bio: auth.user?.bio || "",
  niche: auth.user?.niche || "",
  location: auth.user?.location || "",
  dob: auth.user?.dob || "",
});

const preview = ref(null);

function handleFileChange(e) {
  const file = e.target.files[0];
  if (file) {
    form.value.photo = file;
    preview.value = URL.createObjectURL(file);
  }
}

async function updateProfile() {
  if (form.value.newPassword !== form.value.confirmPassword) {
    return $toast.error("New password and confirm password do not match.");
  }

  const formData = new FormData();
  formData.append("name", form.value.name);
  formData.append("email", form.value.email);
  formData.append("phone", form.value.phone);

  if (auth.user?.role === "promoter") {
    formData.append("tiktok", form.value.tiktok);
    formData.append("facebook", form.value.facebook);
    formData.append("twitter", form.value.twitter);
    formData.append("instagram", form.value.instagram);
    formData.append("youtube", form.value.youtube);
    formData.append("linkedin", form.value.linkedin);
    formData.append("dob", form.value.dob);
    formData.append("niche", form.value.niche);
    formData.append("bio", form.value.bio);
    formData.append("location", form.value.location);
  }

  formData.append("current_password", form.value.currentPassword);
  if (form.value.newPassword)
    formData.append("password", form.value.newPassword);
  if (form.value.photo) formData.append("photo", form.value.photo);

  try {
    await axios.post("https://demo.ngumzo.com/api/update-profile", formData, {
      headers: {
        Authorization: `Bearer ${auth.token}`,
        "Content-Type": "multipart/form-data",
      },
    });

    await auth.refreshUser();
    $toast.success("Profile updated successfully!");
  } catch (error) {
    if (error.response) {
      const status = error.response.status;

      if (status === 422) {
        // Validation errors
        const errors = error.response.data.errors;
        Object.keys(errors).forEach((field) => {
          errors[field].forEach((msg) => {
            $toast.error(msg); // show each validation error
          });
        });
      } else if (status === 403) {
        $toast.error(error.response.data.message || "Unauthorized action.");
      } else {
        $toast.error(error.response.data.message || "An error occurred.");
      }
    } else {
      $toast.error("Network error. Please try again.");
    }

    console.error(error);
  }
}

console.log("User photo:", auth.user?.photo);
</script>
