import { createRouter, createWebHistory } from "vue-router";
import axios from "axios";
import { useAuthStore } from "@/stores/auth";
// Layouts
import HomeLayout from "@/components/HomeLayout.vue";
import PaymentPage from "@/pages/PaymentPage.vue";

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
    path: "/customcv-order",
    name: "CustomOrders",
    component: () => import("@/pages/CVOrder.vue"),
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
    "/how-it-works",
    "/customcv-order",
    "/contact-us",
    "/about-us",
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
