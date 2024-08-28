<template>
  <div class="relative min-h-[2px] overflow-hidden rounded-full bg-gray-300/75">
    <slot name="bar" :value="value" :indeterminate="indeterminate">
      <TProgressBar :value="value" :indeterminate="indeterminate">
        <slot :value="value" :indeterminate="indeterminate"> </slot>
      </TProgressBar>
    </slot>
    <div
      v-if="!hideLabel"
      class="absolute inset-0 flex items-center justify-center bg-inherit bg-clip-text text-center text-xs leading-none text-transparent invert"
    >
      <slot name="label" :value="value">
        <span class="">
          {{ `${value}%` }}
        </span>
      </slot>
    </div>
  </div>
</template>

<script setup>
import { computed, useSlots } from "vue";
import TProgressBar from "./tProgressBar.vue";

const slots = useSlots();
const props = defineProps({
  value: {
    type: Number,
    validator: (val) => val <= 100 && val >= 0,
    default: 0,
  },
  indeterminate: {
    type: Boolean,
    default: false,
  },
  hideLabel: {
    type: Boolean,
    default: false,
  },
});
</script>
