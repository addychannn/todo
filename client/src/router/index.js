import { createRouter, createWebHistory } from "vue-router";
import routes from "./routes";
import { useAuthStore, useSystemStore } from "@/stores";
import { useGuard } from "@/plugins/composables";

const Router = createRouter({
  scrollBehavior: (to, from, savedPosition) => {
    if (savedPosition) {
      return savedPosition;
    } else {
      return { top: 0, left: 0 };
    }
  },
  history: createWebHistory(),
  routes,
});

const routeGuard = (to, from) => {
  return new Promise((resolve, reject) => {
    const authStore = useAuthStore();
    const systemStore = useSystemStore();
    const _guard = useGuard();

    const nearestWithTitle = to.matched
      .slice()
      .reverse()
      .find((r) => r.meta && r.meta.title);

    const previousNearestWithMeta = from.matched
      .slice()
      .reverse()
      .find((r) => r.meta && r.meta.metaTags);

    if (nearestWithTitle) {
      document.title = nearestWithTitle.meta.title;
    } else if (previousNearestWithMeta) {
      document.title = previousNearestWithMeta.meta.title;
    } else {
      document.title = import.meta.env.VITE_SHORT_NAME;
    }

    if (
      to.meta.startPageLoading === undefined ||
      to.meta.startPageLoading === true
    ) {
      systemStore.setLoading("Loading Page...");
    }

    const toRequiresAuth = to.matched.some(
      (record) => record.meta.requiresAuth
    );
    const toRequiresVerified = to.matched.some(
      (record) => record.meta.requiresVerified
    );

    if (to.meta.requiresAuth === true && !authStore.isLoggedIn) {
      reject({ name: "login", query: { redirect: to.fullPath } });
    } else if (to.meta.requiresAuth === false && authStore.isLoggedIn) {
      reject({ name: "HomePage" }); // Redirect to the home page when signed in.
    } else if (toRequiresVerified && !authStore.verified) {
      reject({ name: "unverified", query: { redirect: to.fullPath } });
    } else if (
      authStore.isLoggedIn &&
      !authStore.hasProfileName &&
      to.name != "update-profile" &&
      _guard.can("self_update-profile")
    ) {
      reject({ name: "update-profile", query: { redirect: to.fullPath } });
    } else {
      if (!!to.meta.permissions) {
        if (!_guard.can(to.meta.permissions)) {
          if (!!to.meta.redirect) {
            reject(to.meta.redirect);
          } else {
            reject({ name: "401", query: { redirect: to.fullPath } });
          }
          return; // Ensure the function exits after the rejection.
        }
      }
      resolve();
    }
  });
};

Router.beforeEach(async (to, from, next) => {
  try {
    await routeGuard(to, from);
    next();
  } catch (error) {
    next(error);
  }
});

Router.afterEach((to, from) => {
  const systemStore = useSystemStore();
  systemStore.setLoading(null);
});
export default Router;
