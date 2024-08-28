<template>
  <th>{{ label }}</th>
</template>

<script setup>
import { ref } from "vue";
import { useVModel } from "@vueuse/core";
const props = defineProps({
  label: String,
});
</script>
