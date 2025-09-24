import { createRouter, createWebHistory } from "vue-router";
import axios from "axios";
import { useAuthStore } from "@/stores/auth";
// Layouts
import HomeLayout from "@/components/HomeLayout.vue";
import PaymentPage from "@/pages/PaymentPage.vue";
import AuthLayout from "@/layouts/AuthLayout.vue";
import DashboardLayout from "@/layouts/DashboardLayout.vue";
import DashboardHome from "@/pages/DashboardHome.vue";
import ManageOrders from "@/pages/ManageOrders.vue";
import ManagePayments from "@/pages/ManagePayments.vue";
// Pages
import LoginPage from "@/pages/LoginPage.vue";
import CoverLetter from "@/pages/Dashboard/CoverLetter.vue";
import EmailGenerator from "@/components/EmailGenerator.vue";
import CvRevamp from "@/pages/Dashboard/CvRevamp.vue";
import RegisterPage from "@/pages/RegisterPage.vue";
import SetupPage from "@/pages/SetupPage.vue";
const routes = [
  {
    path: "/",
    component: HomeLayout,
  },
  {
    path: "/order-cv",
    name: "OrderCV",
    component: () => import("@/pages/CVOrder.vue"),
  },
  {
    path: "/custom-cv-order",
    name: "CustomOrders",
    component: () => import("@/pages/CustomCV.vue"),
  },
  {
    path: "/payment/:id",
    name: "PaymentPage",
    component: PaymentPage,
  },
  {
    path: "/contact-us",
    name: "Contacts",
    component: () => import("@/pages/ContactUs.vue"),
  },
  {
    path: "/about-us",
    name: "AboutUs",
    component: () => import("@/pages/AboutUs.vue"),
  },
   {
    path: "/services",
    name: "Services",
    component: () => import("@/pages/Services.vue"),
  },
   {
    path: "/pricing",
    name: "Pricing",
    component: () => import("@/pages/Pricing.vue"),
  },
  
  {
    path: "/free-review",
    name: "FreeReview",
    component: () => import("@/pages/CvUpload.vue"),
  },
   {
    path: "/ai",
    name: "AI",
    component: () => import("@/pages/AiReview.vue"),
  },
  //backend
  {
    path: "/admin",
    component: AuthLayout,
    children: [
      {
        path: "",
        component: LoginPage,
      },
    ],
  },
    {
    path: "/register",
    component: AuthLayout,
    children: [
      {
        path: "",
        component: RegisterPage,
      },
    ],
  },
   {
    path: "/profile-setup",
    component: AuthLayout,
    children: [
      {
        path: "",
        component: SetupPage,
      },
    ],
  },
   {
    path: "/dashboard",
    component: DashboardLayout,
    meta: {
      title: "Dashboard",
    },
    children: [
      {
        path: "",
        component: DashboardHome,
      },
    ],
  },
   {
    path: "/manage-orders",
    component: DashboardLayout,
    meta: {
      title: "Manage Orders",
    },
    children: [
      {
        path: "",
        component: ManageOrders,
      },
    ],
  },
   {
    path: "/manage-payments",
    component: DashboardLayout,
    meta: {
      title: "Manage Payments",
    },
    children: [
      {
        path: "",
        component: ManagePayments,
      },
    ],
  },
  {
    path: "/cover-letter-generator",
    component: DashboardLayout,
    meta: {
      title: "Generate Cover Letters",
    },
    children: [
      {
        path: "",
        component: CoverLetter,
      },
    ],
  },
    {
    path: "/email-template-generator",
    component: DashboardLayout,
    meta: {
      title: "Email Template Generator",
    },
    children: [
      {
        path: "",
        component: EmailGenerator,
      },
    ],
  },
   {
    path: "/cv-revamp-generator",
    component: DashboardLayout,
    meta: {
      title: "Email Template Generator",
    },
    children: [
      {
        path: "",
        component: CvRevamp,
      },
    ],
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach(async (to, from, next) => {
  const auth = useAuthStore();

  const publicRoutes = [
    "/",
    "/payment/:id",
    "/order-cv",
    "/admin",
    '/register',
    "/how-it-works",
    "/custom-cv-order",
    "/contact-us",
    "/about-us",
    "/services",
    "/pricing",
     "/free-review",
    "/ai",
     "/profile-setup"
  ];
  const isPublic = to.matched.some((route) =>
    publicRoutes.includes(route.path)
  );

  if (!isPublic && !auth.token) {
    return next("/admin");
  }

  if (!isPublic && auth.token) {
    try {
      await axios.get("/api/auth/verify-token", {
        headers: { Authorization: `Bearer ${auth.token}` },
      });
      return next();
    } catch (err) {
      auth.clearToken();
      return next("/admin");
    }
  }

  return next();
});

export default router;
