import { defineAsyncComponent } from "vue";

export default {
  common: defineAsyncComponent(() => import("./common.vue")),
  tmp: defineAsyncComponent(() => import("./tmp.vue")),
  dateRange: defineAsyncComponent(() => import("./dateRange.vue")),
  barangay: defineAsyncComponent(() => import("./barangay.vue")),
};
