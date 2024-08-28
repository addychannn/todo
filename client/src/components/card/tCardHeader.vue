<template>
  <component
    :is="tag"
    class="flex min-h-[2rem] shrink-0 grow-0 items-center rounded-t-2xl border-b border-foreground/25 bg-page-background px-3 py-2"
  >
    <slot></slot>
  </component>
</template>

<script setup>
const props = defineProps({
  tag: {
    type: String,
    default: "div",
  },
});
</script>
