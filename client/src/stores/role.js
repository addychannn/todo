import { defineStore } from "pinia";
import { getters, actions } from "./searchHistory";

export const useRoleStore = defineStore("role", {
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
