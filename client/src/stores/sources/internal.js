import { defineStore } from "pinia";
import { state, getters, actions } from "../searchHistory";

export const useInternalStore = defineStore("internal", {
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
