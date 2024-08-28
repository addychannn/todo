import { defineStore } from "pinia";
import { getters, actions } from "./searchHistory";

export const usePermissionStore = defineStore("permission", {
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
