<template>
  <component :is="components[type] ?? components.text" />
</template>

<script setup>
import { defineAsyncComponent, ref } from "vue";
const props = defineProps({
  type: {
    type: String,
    default: "text",
    validator: (val) => ["text", "radio"].indexOf(val) > -1,
  },
});

const components = {
  text: defineAsyncComponent(() => import("./text.vue")),
  radio: defineAsyncComponent(() => import("./radiobox.vue")),
};
</script>
