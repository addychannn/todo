import { defineStore } from "pinia";
import { getters, actions } from "./searchHistory";

export const useUserStore = defineStore("user", {
  state: () => ({
    searchHistory: [],
  }),
  getters: {
    ...getters,
  },
  actions: {
    ...actions,
  },
});
