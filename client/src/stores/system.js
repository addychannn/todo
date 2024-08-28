import { defineStore } from "pinia";

export const useSystemStore = defineStore("system", {
  state: () => ({
    loadingMessage: null,
    theme: {
      dark: false,
    },
    settings: {
      sidebar: {
        collapsed: false,
      },
      navbar: {
        fixed: false,
      },
      pwa: {
        doNotShow: false,
      },
    },
  }),
  getters: {
    isLoading(state) {
      return !!state.loadingMessage;
    },
  },
  actions: {
    setLoading(message = null) {
      this.loadingMessage = message;
    },
    toggleTheme(joke = false) {
      if (!joke) {
        this.theme.dark = !this.theme.dark;
      }
      if (this.theme.dark) {
        document.documentElement.classList.add("dark");
      } else {
        document.documentElement.classList.remove("dark");
      }
    },
    toggleFixedNavbar() {
      this.settings.navbar.fixed = !this.settings.navbar.fixed;
    },
  },
});
