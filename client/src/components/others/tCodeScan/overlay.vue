<template>
  <div
    class="pointer-events-none relative aspect-square border border-light"
    :class="[
      isPortrait && 'w-[95dvw] min-w-[5rem] max-w-[75%]',
      !isPortrait && 'h-[95dvw] max-h-[75%] min-h-[5rem]',
    ]"
  >
    <div
      class="relative flex h-full items-center justify-stretch overflow-hidden"
    >
      <div
        class="absolute inset-x-0 top-1/2 h-[2px] -translate-y-1/2 animate-ping bg-negative shadow-md"
      ></div>
      <div
        class="absolute inset-x-0 top-1/2 h-[1px] -translate-y-1/2 bg-negative/25 shadow-md"
      ></div>
    </div>
    <div
      class="absolute -left-1 -top-1 aspect-square w-1/12 border-l-8 border-t-8 border-primary"
    />
    <div
      class="absolute -right-1 -top-1 aspect-square w-1/12 border-r-8 border-t-8 border-primary"
    />
    <div
      class="absolute -bottom-1 -left-1 aspect-square w-1/12 border-b-8 border-l-8 border-primary"
    />
    <div
      class="absolute -bottom-1 -right-1 aspect-square w-1/12 border-b-8 border-r-8 border-primary"
    />
  </div>
</template>

<script setup>
import { ref } from "vue";
const props = defineProps({
  isPortrait: {
    type: Boolean,
    default: false,
  },
});
</script>
