<template>
  <component :is="'span'" :class="className" aria-hidden="true">
    <slot>
      {{ name }}
    </slot>
  </component>
</template>

<script setup>
import { computed, ref } from "vue";

const props = defineProps({
  name: String,
  type: {
    type: String,
    default: "outlined",
    validator: (val) => {
      return (
        ["filled", "outlined", "round", "sharp", "two-tone"].indexOf(val) > -1
      );
    },
  },
  size: {
    type: String,
    default: "md",
    validator: (val) => {
      return (
        ["xs", "sm", "md", "lg", "xl", "2xl", "3xl", "4xl", "5xl"].indexOf(
          val
        ) > -1
      );
    },
  },
});

const className = computed({
  get() {
    return [
      "!leading-none select-none",
      props.type == "filled" && "material-icons",
      props.type == "outlined" && "material-icons-outlined",
      props.type == "round" && "material-icons-round",
      props.type == "sharp" && "material-icons-sharp",
      props.type == "two-tone" && "material-icons-two-tone",
      `mi-${props.size}`,
    ];
  },
});
</script>

<style scoped lang="scss">
.mi-xs {
  @apply text-sm;
}
.mi-sm {
  @apply text-lg;
}
.mi-md {
  @apply text-2xl;
}
.mi-lg {
  @apply text-4xl;
}
.mi-xl {
  @apply text-5xl;
}
.mi-2xl {
  @apply text-6xl;
}
.mi-3xl {
  @apply text-7xl;
}
.mi-4xl {
  @apply text-8xl;
}
.mi-5xl {
  @apply text-9xl;
}
</style>
