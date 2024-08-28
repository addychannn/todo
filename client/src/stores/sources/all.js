import { defineStore } from "pinia";
import { state, getters, actions } from "../searchHistory";

export const useAllStore = defineStore("all", {
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
