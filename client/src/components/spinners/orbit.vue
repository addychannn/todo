<template>
  <svg
    version="1.1"
    id="L3"
    xmlns="http://www.w3.org/2000/svg"
    xmlns:xlink="http://www.w3.org/1999/xlink"
    x="0px"
    y="0px"
    viewBox="0 0 100 100"
    enable-background="new 0 0 0 0"
    xml:space="preserve"
  >
    <circle
      fill="none"
      :stroke="color"
      stroke-width="14"
      cx="50"
      cy="50"
      r="40"
      style="opacity: 0.25"
    />
    <circle
      :fill="color"
      :stroke="color"
      stroke-width="1"
      cx="10.5"
      cy="54"
      r="6"
    >
      <animateTransform
        attributeName="transform"
        :dur="`${speed}ms`"
        type="rotate"
        from="0 50 48"
        to="360 50 52"
        repeatCount="indefinite"
      />
    </circle>
  </svg>
</template>

<script setup>
const props = defineProps({
  color: {
    type: String,
    default: "hsl(var(--color-foreground) / var(--tw-bg-opacity))",
  },
  speed: {
    type: Number,
    default: 2000,
  },
});
</script>
