import { createRouter, createWebHistory } from "vue-router";
import axios from "axios";
import { useAuthStore } from "@/stores/auth";

// Layouts
import HomeLayout from "@/components/HomeLayout.vue";
import AuthLayout from "@/layouts/AuthLayout.vue";
import DashboardLayout from "@/layouts/DashboardLayout.vue";
// Pages
import LoginPage from "@/pages/LoginPage.vue";
import RegisterPage from "@/pages/RegisterPage.vue";
import DashboardHome from "@/pages/DashboardHome.vue";
import AddCampaign from "@/pages/AddCampaign.vue";
import ProfileEdit from "@/pages/ProfileEdit.vue";
import MyRequests from "@/pages/MyRequests.vue";
import Subscription from "@/pages/Subscription.vue";
import PromoterRequests from "@/pages/PromoterRequests.vue";
const routes = [
  {
    path: "/",
    component: HomeLayout,
  },
  {
    path: "/login",
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
    path: "/order-cv",
    name: "OrderCV",
    component: () => import("@/pages/HowItWorks.vue"),
  },
  {
    path: "/contact-us",
    name: "Contacts",
    component: () => import("@/pages/ContactUs.vue"),
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
    path: "/manage-campaigns",
    component: DashboardLayout,
    meta: {
      title: "Manage Campaigns",
    },
    children: [
      {
        path: "",
        component: AddCampaign,
      },
    ],
  },
  
  {
    path: "/my-profile",
    component: DashboardLayout,
    meta: {
      title: "Manage Profile",
    },
    children: [
      {
        path: "",
        component: ProfileEdit,
      },
    ],
  },
  {
    path: "/subscription",
    component: DashboardLayout,
    meta: {
      title: "Subscription",
    },
    children: [
      {
        path: "",
        component: Subscription,
      },
    ],
  },
  {
    path: "/my-requests",
    component: DashboardLayout,
    meta: {
      title: "Manage Requests",
    },
    children: [
      {
        path: "",
        component: MyRequests,
      },
    ],
  },
  {
    path: "/requests",
    component: DashboardLayout,
    meta: {
      title: "Manage Requests",
    },
    children: [
      {
        path: "",
        component: PromoterRequests,
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
    "/login",
    "/register",
    "/campaigns/:id/:slug",
    "/campaigns",
    "/how-it-works",
    "/contact-us",
  ];
  const isPublic = to.matched.some((route) =>
    publicRoutes.includes(route.path)
  );

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
