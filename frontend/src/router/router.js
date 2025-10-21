import { createRouter, createWebHistory } from "vue-router";
import axios from "axios";
import { useAuthStore } from "@/stores/auth";

// Layouts & Pages
import HomeLayout from "@/components/HomeLayout.vue";
import PaymentPage from "@/pages/PaymentPage.vue";
import AuthLayout from "@/layouts/AuthLayout.vue";
import DashboardLayout from "@/layouts/DashboardLayout.vue";
import DashboardHome from "@/pages/Dashboard/DashboardHome.vue";
import ManageOrders from "@/pages/ManageOrders.vue";
import ManagePayments from "@/pages/ManagePayments.vue";
import LoginPage from "@/pages/LoginPage.vue";
import CoverLetter from "@/pages/Dashboard/CoverLetter.vue";
import EmailGenerator from "@/components/EmailGenerator.vue";
import CvRevamp from "@/pages/Dashboard/CvRevamp.vue";
import RegisterPage from "@/pages/RegisterPage.vue";
import SetupPage from "@/pages/SetupPage.vue";
import ManageUsers from "@/pages/Dashboard/ManageUsers.vue";
import ManageJobs from "@/pages/Dashboard/ManageJobs.vue";
import ManageSavedJobs from "@/pages/Dashboard/ManageSavedJobs.vue"
import AddJobs from "@/pages/Dashboard/AddJobs.vue";
import Profile from "@/pages/Dashboard/Profile.vue";
import Plans from "@/pages/Dashboard/Plans.vue";
import PremiumPlans from "@/pages/Dashboard/PremiumPlans.vue";
import UserJobs from "@/pages/Dashboard/UserJobs.vue";
import PremiumUserJobs from "@/pages/Dashboard/PremiumUserJobs.vue";
import UserActivity from "@/pages/Dashboard/UserActivity.vue";
import ManageCategories from "@/pages/Dashboard/ManageCategories.vue";

const routes = [
  { path: "/", component: HomeLayout },
  { path: "/order-cv", name: "OrderCV", component: () => import("@/pages/CVOrder.vue") },
  { path: "/custom-cv-order", name: "CustomOrders", component: () => import("@/pages/CustomCV.vue") },
  { path: "/payment/:id", name: "PaymentPage", component: PaymentPage },
  { path: "/contact-us", name: "Contacts", component: () => import("@/pages/ContactUs.vue") },
  { path: "/about-us", name: "AboutUs", component: () => import("@/pages/AboutUs.vue") },
  { path: "/services", name: "Services", component: () => import("@/pages/Services.vue") },
  { path: "/pricing", name: "Pricing", component: () => import("@/pages/Pricing.vue") },
  { path: "/free-review", name: "FreeReview", component: () => import("@/pages/CvUpload.vue") },
  { path: "/ai", name: "AI", component: () => import("@/pages/AiReview.vue") },
  { path: "/jobs", name: "Jobs", component: () => import("@/pages/AiJobs.vue") },


  

  // Auth routes
  {
    path: "/login",
    component: AuthLayout,
    children: [{ path: "", component: LoginPage }],
  },
  {
    path: "/register",
    component: AuthLayout,
    children: [{ path: "", component: RegisterPage }],
  },
  {
    path: "/profile-setup",
    component: AuthLayout,
    children: [{ path: "", component: SetupPage }],
  },

  // Dashboard routes
  {
    path: "/dashboard",
    component: DashboardLayout,
    meta: { title: "Dashboard" },
    children: [{ path: "", component: DashboardHome }],
  },
  {
    path: "/manage-orders",
    component: DashboardLayout,
    meta: { title: "Manage Orders" },
    children: [{ path: "", component: ManageOrders }],
  },
  {
    path: "/manage-users",
    component: DashboardLayout,
    meta: { title: "Manage Users" },
    children: [{ path: "", component: ManageUsers }],
  },
  {
    path: "/manage-jobs",
    component: DashboardLayout,
    meta: { title: "Manage Jobs" },
    children: [{ path: "", component: ManageJobs }],
  },

{
    path: "/manage-save-jobs",
    component: DashboardLayout,
    meta: { title: "Manage Saved Jobs" },
    children: [{ path: "", component: ManageSavedJobs }],
  },

  
  {
    path: "/manage-categories",
    component: DashboardLayout,
    meta: { title: "Manage Categories" },
    children: [{ path: "", component: ManageCategories }],
  },
  {
    path: "/add-job",
    component: DashboardLayout,
    meta: { title: "Add Jobs" },
    children: [{ path: "", component: AddJobs }],
  },
  {
    path: "/manage-payments",
    component: DashboardLayout,
    meta: { title: "Manage Payments" },
    children: [{ path: "", component: ManagePayments }],
  },
  {
    path: "/cover-letter-generator",
    component: DashboardLayout,
    meta: { title: "Generate Cover Letters" },
    children: [{ path: "", component: CoverLetter }],
  },
  {
    path: "/email-template-generator",
    component: DashboardLayout,
    meta: { title: "Email Template Generator" },
    children: [{ path: "", component: EmailGenerator }],
  },
  {
    path: "/cv-revamp-generator",
    component: DashboardLayout,
    meta: { title: "CV Revamp Generator" },
    children: [{ path: "", component: CvRevamp }],
  },
  {
    path: "/my-plans",
    component: DashboardLayout,
    meta: { title: "My Plans" },
    children: [{ path: "", component: Plans }],
  },
{
    path: "/premium-plans",
    component: DashboardLayout,
    meta: { title: "My Plans" },
    children: [{ path: "", component: PremiumPlans }],
  },

  
  {
    path: "/profile",
    component: DashboardLayout,
    meta: { title: "Profile Summary" },
    children: [{ path: "", component: Profile }],
  },
  {
    path: "/browse-jobs",
    component: DashboardLayout,
    meta: { title: "Browse Jobs" },
    children: [{ path: "", component: UserJobs }],
  },
{
    path: "/job-activities",
    component: DashboardLayout,
    meta: { title: "My Jobs" },
    children: [{ path: "", component: PremiumUserJobs }],
  },

  
  {
    path: "/browse-activities",
    component: DashboardLayout,
    meta: { title: "Activities History" },
    children: [{ path: "", component: UserActivity }],
  },
  {
  path: '/gmail-callback',
  name: 'GmailCallback',
  component: () => import('@/pages/Dashboard/GmailCallback.vue')
}

];

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    // 👇 Always scroll to the top when navigating
    return { top: 0 };
  },
});

// ✅ Auth guard
router.beforeEach(async (to, from, next) => {
  const auth = useAuthStore();

  const publicRoutes = [
    "/",
    "/payment/:id",
    "/order-cv",
    "/login",
    "/gmail-callback",
    "/register",
    "/how-it-works",
    "/custom-cv-order",
    "/contact-us",
    "/jobs",
    "/about-us",
    "/services",
    "/pricing",
    "/free-review",
    "/ai",
    "/ai-hero",
    "/ai-pricing",
    "/ai-services",
    "/profile-setup",
  ];

  const isPublic = to.matched.some((route) => publicRoutes.includes(route.path));

  if (!isPublic && !auth.token) {
    return next("/login");
  }

  if (!isPublic && auth.token) {
    try {
      await axios.get("/api/auth/verify-token", {
        headers: { Authorization: `Bearer ${auth.token}` },
      });
      return next();
    } catch (err) {
      auth.clearToken();
      return next("/login");
    }
  }

  return next();
});

export default router;
