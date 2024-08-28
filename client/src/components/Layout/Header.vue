<template>
  <nav class="layout-header" :class="[fixed && 'layout-header-fixed']">
    <slot></slot>
  </nav>
</template>

<script setup>
const props = defineProps({
  fixed: {
    type: Boolean,
    default: false,
  },
});
</script>
