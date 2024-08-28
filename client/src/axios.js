import Axios from "axios";
import router from "@/router";
import { notify } from "@/scripts";
import { useAuthStore } from "./stores";

let options = {
  withCredentials: import.meta.env.VITE_AUTH_TOKEN != "true",
  headers: {
    Accept: "application/json",
    Authorization: null,
  },
};

const axios = Axios.create(options);

axios.interceptors.request.use(
  (config) => {
    if (import.meta.env.VITE_AUTH_TOKEN == "true") {
      const useAuth = useAuthStore();
      if (useAuth.isLoggedIn) {
        config.headers.Authorization = useAuth.access_token;
      }
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);
axios.interceptors.response.use(
  (response) => {
    return response;
  },
  (error) => {
    const useAuth = useAuthStore();
    if (error.response.status == 401) {
      const fullPath = router.currentRoute.value.fullPath;
      if (useAuth.isLoggedIn) {
        useAuth.reset();
      }
      if (router.currentRoute.value.name != "login") {
        router.push({
          name: "login",
          query: { redirect: fullPath },
        });
      }
    }
    if (!error.response?.data?.error_code) {
      notify({
        title: error.response ? error.response.data.message : error.message,
        type: "negative",
        text: error.response ? error.message : null,
      });
    }
    return Promise.reject(error);
  }
);

let api = { ...axios };
api.defaults.baseURL = import.meta.env.VITE_SERVER;
if (import.meta.env.VITE_AUTH_TOKEN != "true") {
  api.get("/csrf-cookie");
}

export { api, axios };
