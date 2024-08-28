<template>
  <div class="relative grid grid-cols-12 border-b border-foreground/25 py-1">
    <div
      class="col-span-full row-start-1 flex items-start px-2 text-lg font-bold text-gray-400 md:col-span-3"
    >
      <slot name="label">
        {{ label }}
      </slot>
    </div>
    <div
      class="col-span-full row-start-2 flex items-center px-5 font-semibold md:col-span-7 md:col-start-4 md:row-start-1 md:px-2"
    >
      <slot>
        {{ value }}
      </slot>
    </div>
    <div
      class="col-span-2 col-start-11 row-start-1 flex items-start justify-end md:col-start-11"
    >
      <slot name="icon">
        <TIcon v-if="!!icon" :name="icon" />
      </slot>
    </div>
    <FocusHelper color="bg-foreground" :disabled="disableFocus" />
  </div>
</template>

<script setup>
import { ref } from "vue";
const props = defineProps({
  label: [String, Number],
  value: {
    type: [String, Number],
    default: "",
  },
  icon: {
    type: String,
    default: "edit",
  },
  disableFocus: {
    type: Boolean,
    default: false,
  },
});
</script>
