<template>
  <div class="flex flex-col items-center">
    <div class="relative mb-2 flex max-w-xs flex-col overflow-hidden">
      <ErrorTemplate :code="code" :error="error" :description="description" />
    </div>
    <slot name="links">
      <NavLinks :links="links" />
    </slot>
  </div>
</template>

<script setup>
import NavLinks from "./links.vue";

const props = defineProps({
  code: { type: Number, default: null },
  error: { type: String, default: null },
  description: { type: String, default: null },
  links: {
    type: Object,
    default: [],
  },
});
</script>
