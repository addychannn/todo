<template>
  <div
    class="relative flex select-none items-center overflow-hidden rounded-lg border border-foreground/25 bg-background-accent px-2 py-1 shadow-md transition-all"
    :class="[
      active && 'bg-dark text-light dark:bg-primary/75',
      canEdit && 'cursor-pointer hover:scale-[1.02]',
    ]"
    :role="canEdit && 'button'"
  >
    <FocusHelper :disabled="!canEdit" color="bg-foreground" />
    <div class="flex-auto">
      <div class="font-semibold">
        {{ Helpers.dashToHuman(modelValue.name) }}
      </div>
      <div class="line-clamp-2 h-8 px-2 text-xs">
        {{ modelValue.description }}
      </div>
    </div>
    <div v-if="active" class="flex items-center">
      <div
        class="flex aspect-square h-6 w-6 items-center justify-center rounded-full border border-light/50 bg-light/25 leading-none"
      >
        <TIcon name="check" class="text-light" size="sm" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { Helpers } from "@/scripts";
const props = defineProps({
  modelValue: Object,
  active: {
    type: Boolean,
    default: false,
  },
  canEdit: {
    type: Boolean,
    default: false,
  },
});
</script>
