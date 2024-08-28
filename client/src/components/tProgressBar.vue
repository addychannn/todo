<template>
  <template v-if="indeterminate">
    <div
      class="absolute inset-y-0 left-[50%] animate-progInc select-none overflow-hidden rounded-full bg-primary"
    ></div>
    <div
      class="absolute inset-y-0 left-[50%] animate-progDec select-none overflow-hidden rounded-full bg-primary"
    ></div>
  </template>
  <div
    v-else
    class="absolute inset-y-0 left-0 flex select-none items-center justify-center overflow-hidden rounded-full bg-primary transition-all duration-500"
    :style="{ width: `${value}%` }"
  >
    <slot :value="value"></slot>
  </div>
</template>
<script setup>
const props = defineProps({
  value: Number,
  indeterminate: {
    type: Boolean,
    default: false,
  },
});
</script>
