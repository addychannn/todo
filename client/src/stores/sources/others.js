import { defineStore } from "pinia";
import { state, getters, actions } from "../searchHistory";

export const useOthersStore = defineStore("others", {
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
