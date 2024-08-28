<template>
  <div :class="classNames" :style="`--progress: ${value}%;`" ref="progressBar">
    <div
      v-if="!indeterminate"
      class="progress-text"
      :class="[!showLabel && '!hidden']"
      ref="progressLabel"
    >
      <slot :value="value"> {{ value }}% </slot>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from "vue";
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
  stripe: {
    type: Boolean,
    default: false,
  },
  animated: {
    type: Boolean,
    default: false,
  },
  vertical: {
    type: Boolean,
    default: false,
  },
});

const progressBar = ref(null);
const progressLabel = ref(null);

const classNames = computed(() => [
  props.indeterminate && "progress-indeterminate",
  props.stripe && !props.indeterminate && "progress-stripe",
  props.stripe && !props.indeterminate && props.animated && "progress-animated",
  props.vertical ? "progress-vertical" : "progress-bar",
]);

const showLabel = computed(() => {
  let pb = progressBar.value?.offsetHeight ?? 0;
  let pl = progressLabel.value?.offsetHeight ?? 0;
  return pb >= pl;
});

const getColorBrightness = (color) => {
  const rgb = color.match(/\d+/g).map(Number);
  const brightness = Math.round(
    (rgb[0] * 299 + rgb[1] * 587 + rgb[2] * 114) / 1000
  );
  return brightness;
};
</script>

<style scoped lang="scss">
.progress-vertical {
  @apply relative h-full min-w-[2px] overflow-hidden rounded-md bg-gray-300/75  text-xs;

  &::before {
    @apply absolute inset-x-0 h-[var(--progress)] bg-primary transition-all content-[''];
  }

  &.progress-animated {
    &::before {
      @apply animate-progStripeV;
    }
  }

  &.progress-indeterminate {
    &::before {
      @apply animate-progIncV;
    }
    &::after {
      @apply absolute inset-y-0 animate-progDecV bg-primary transition-all content-[''];
    }
  }
}

.progress-bar {
  @apply relative min-h-[2px] w-full overflow-hidden rounded-md bg-gray-300/75  text-xs;

  &::before {
    @apply absolute inset-y-0 w-[var(--progress)] bg-primary transition-all content-[''];
  }

  &.progress-animated {
    &::before {
      @apply animate-progStripe;
    }
  }

  &.progress-indeterminate {
    &::before {
      @apply animate-progInc;
    }
    &::after {
      @apply absolute inset-y-0 animate-progDec bg-primary transition-all content-[''];
    }
  }
}

.progress-bar,
.progress-vertical {
  &.progress-stripe {
    &::before {
      @apply bg-stripe;
    }
  }
}
.progress-text {
  @apply pointer-events-none absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 select-none font-normal leading-none text-light;
  text-shadow: 1px 1px 0 #000, -1px -1px 0 #000, 1px -1px 0 #000,
    -1px 1px 0 #000, 1px 1px 0 #000;
}
</style>
