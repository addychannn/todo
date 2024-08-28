import { defineStore } from "pinia";
import { useSystemStore as systemStore } from "./system";
import { Helpers } from "@/scripts";

export const useAuthStore = defineStore("auth", {
  state: () => ({
    email: null,
    username: null,
    disabled: false,
    access_token: null,
    verified: false,

    profile: {
      birthdate: null,
      first_name: null,
      full_name: null,
      gender: null,
      image: null,
      images: [],
      last_name: null,
      middle_ame: null,
      nickname: null,
      suffix: null,
      addresses: [],
    },

    roles: [],
    permissions: [],

    pin: null,
    locked: null,
  }),
  getters: {
    isLoggedIn(state) {
      return import.meta.env.VUE_APP_AUTH_TOKEN == "true"
        ? state.access_token !== null
        : state.username !== null;
    },
    hasProfileName(state) {
      return !!state.profile?.first_name && !!state.profile?.last_name;
    },
    isVerified(state) {
      return state.verified;
    },
    isDisabled(state) {
      return state.disabled;
    },
    getProfile(state) {
      return state.profile;
    },
    getAccount(state) {
      return {
        email: state.email,
        username: state.username,
        disabled: state.disabled,
        isVerified: state.verified,
      };
    },
    getAccess(state) {
      return {
        permissions: state.permissions,
        roles: state.roles,
      };
    },
    getAccessToken(state) {
      return state.access_token;
    },
    getUserPermissions(state) {
      return state.permissions;
    },
    getRoles(state) {
      return state.roles;
    },
  },
  actions: {
    async login(payload) {
      return new Promise((resolve, reject) => {
        this.$api
          .post("/auth/login", payload)
          .then((response) => {
            let data = response.data;
            if (import.meta.env.VITE_AUTH_TOKEN == "true") {
              this.access_token = `${data.token_type} ${data.access_token}`;
            }
            this.disabled = data.disabled;
            this.verified = data.verified;
            this.roles = data.roles;
            this.permissions = data.permissions;
            this.profile = data.profile;
            this.username = data.username;
            this.email = data.email;

            resolve(response);
          })
          .catch((error) => {
            reject(error);
          });
      });
    },
    async logout() {
      return new Promise((resolve, reject) => {
        systemStore().setLoading("Logging out...");
        this.$api
          .post("/auth/logout")
          .then((response) => {
            resolve(response);
          })
          .catch((error) => {
            reject(error);
          })
          .finally(() => {
            this.$reset();
            systemStore().setLoading(null);
            this.$router.push({ name: "login" });
          });
      });
    },
    async getPermissions() {
      return new Promise((resolve, reject) => {
        this.$api
          .get("/auth/permissions")
          .then((response) => {
            Object.assign(this, response.data.data);
            resolve(response);
          })
          .catch((error) => {
            reject(error);
          });
      });
    },

    reset() {
      this.$reset();
    },

    setPin(pin) {
      this.pin = Helpers.crypt(pin);
    },
    lock() {
      this.locked = new Date();
    },
    unlock(pin) {
      if (this.verifyPin(pin)) {
        this.locked = null;
      }
    },
    resetPin() {
      this.locked = this.pin = null;
    },

    verifyPin(pin) {
      return this.pin == Helpers.crypt(pin);
    },
  },
});
