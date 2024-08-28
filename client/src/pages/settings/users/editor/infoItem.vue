<template>
  <component
    :is="tag"
    class="group relative select-none text-lg transition-all md:table-row"
    :class="[
      hoverable && 'cursor-pointer hover:scale-[1.01] hover:bg-foreground/20',
    ]"
  >
    <div
      v-if="!!slots.icon"
      class="w-5 border-b border-foreground/25 px-2 py-3 align-middle group-last:border-transparent md:table-cell"
    >
      <slot name="icon" :icon="icon" :iconSize="iconSize">
        <TIcon v-if="!!icon" :name="icon" :size="iconSize" />
      </slot>
    </div>
    <div
      class="border-b border-foreground/25 px-2 py-3 align-middle group-last:border-transparent md:table-cell md:w-40"
    >
      <div
        class="h-full text-sm font-semibold text-gray-600 dark:text-gray-300"
      >
        {{ label }}
      </div>
    </div>
    <div
      class="border-b border-foreground/25 px-2 py-3 align-middle group-last:border-transparent md:table-cell"
    >
      <slot :value="value">
        {{ value }}
      </slot>
    </div>
  </component>
</template>

<script setup>
import { ref, useSlots } from "vue";

const slots = useSlots();
const props = defineProps({
  label: String,
  tag: {
    type: String,
    default: "div",
  },
  value: {
    type: String,
    default: null,
  },
  icon: {
    type: String,
    default: null,
  },
  iconSize: {
    type: String,
    default: "md",
  },
  hoverable: {
    type: Boolean,
    default: false,
  },
});
</script>
