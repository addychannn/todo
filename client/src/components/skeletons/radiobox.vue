<template>
  <div class="inline-flex w-24 items-center gap-1 rounded-md">
    <div class="aspect-square w-5 animate-pulse rounded-full bg-gray-300" />
    <div class="h-3 flex-auto animate-pulse bg-gray-300" />
  </div>
</template>

<script setup>
import { ref } from "vue";
const props = defineProps({});
</script>
