<template>
  <div
    class="group/tgroup relative rounded-lg border border-slate-400 transition-colors focus-within:border-primary"
    :class="classNames"
  >
    <div
      v-if="!!label"
      class="absolute top-0 -translate-y-1/2 rounded-full border border-slate-400 px-2 text-xs font-semibold uppercase leading-none text-gray-400/75 group-focus-within/tgroup:border-primary group-focus-within/tgroup:text-primary"
      :class="labelClass"
    >
      <div :class="[error && 'animate-shake ']">
        {{ label }}
      </div>
    </div>
    <div
      class="absolute inset-x-0 bottom-0 translate-y-full px-2 pt-1 text-[11px] font-semibold leading-none"
    >
      <transition
        enter-from-class="-translate-y-full opacity-0"
        leave-to-class="-translate-y-full opacity-0"
        enter-active-class="transition duration-300"
        leave-active-class="transition duration-300"
      >
        <div
          v-if="error"
          class="rounded-full border border-negative bg-negative/75 px-1 leading-tight text-light"
        >
          {{ errorMessage }}
        </div>
      </transition>
    </div>
    <slot></slot>
  </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
  label: {
    type: String,
    default: null,
  },
  labelPos: {
    type: String,
    default: "center",
    validator: (val) => ["left", "center", "right"].indexOf(val) > -1,
  },
  bg: {
    type: String,
    default: "bg-light",
  },
  error: {
    type: Boolean,
    default: false,
  },
  errorMessage: {
    type: String,
    default: null,
  },
});

const classNames = computed(() => [
  props.bg,
  props.error && "!border-negative mb-5",
]);

const labelClass = computed(() => [
  props.bg,
  props.labelPos == "left" && "left-2",
  props.labelPos == "center" && "left-1/2 -translate-x-1/2",
  props.labelPos == "right" && "right-2",
  props.error && "!text-negative !border-negative",
]);
</script>
