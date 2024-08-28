<template>
  <component
    :is="tag"
    class="flex flex-col rounded-2xl border border-foreground/25 bg-background-accent shadow-md after:shadow-slate-700 after:dark:shadow-slate-100"
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
