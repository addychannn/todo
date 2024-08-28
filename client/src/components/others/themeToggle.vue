<template>
  <button
    class="relative my-1 leading-none transition-colors"
    @click="systemStore.toggleTheme(false)"
  >
    <div
      class="group relative flex items-center justify-center gap-1 leading-none"
    >
      <span class="sr-only">
        {{ systemStore.theme.dark ? "light" : "dark" }} mode
      </span>
      <TIcon :name="systemStore.theme.dark ? lightIcon : darkIcon" />
      <div v-if="!!label" class="font-semibold leading-none">
        {{ label }}
      </div>
    </div>
    <FocusHelper :color="focusClass" />
  </button>
</template>

<script setup>
import { useSystemStore } from "@/stores";

const systemStore = useSystemStore();

const props = defineProps({
  label: {
    type: String,
    default: null,
  },
  lightIcon: {
    type: String,
    default: "light_mode",
  },
  darkIcon: {
    type: String,
    default: "dark_mode",
  },
  focusClass: {
    type: [String, Object],
    default: "bg-foreground",
  },
});
</script>
