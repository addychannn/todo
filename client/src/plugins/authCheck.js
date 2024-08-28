import { watch } from "vue";
import { useAuthStore } from "../stores";
import router from "../router";

export default {
  install: (app, options) => {
    const authStore = useAuthStore();
    watch(
      () => authStore.isLoggedIn,
      (val) => {
        let to = {};
        if (!val) {
          to = {
            name: "login",
            query: { redirect: router.currentRoute.value.fullPath },
          };
        } else {
          to = router.currentRoute.value.query?.redirect ?? {
            name: "HomePage",
          };
        }
        router.push(to);
      }
    );
  },
};
