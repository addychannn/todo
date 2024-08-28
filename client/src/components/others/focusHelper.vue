<template>
  <component
    :is="tag"
    class="pointer-events-none absolute inset-0 !block h-full w-full transition-opacity"
    :class="[color, hover ? 'opacity-20' : 'opacity-0']"
    ref="focushelperel"
  >
  </component>
</template>
<script setup>
import { onBeforeUnmount, onMounted, ref } from "vue";

const props = defineProps({
  tag: {
    type: String,
    default: "span",
  },
  color: {
    type: String,
    default: "bg-foreground",
  },
  disabled: {
    type: Boolean,
    default: false,
  },
});

const events = {
  active: ["mouseenter", "focus"],
  inactive: ["mouseleave", "blur"],
};

const focushelperel = ref();

const hover = ref(false);

const onParentMouseEnter = () => {
  if (!props.disabled) {
    hover.value = true;
  }
};

const onPrentMouseLeave = () => {
  hover.value = false;
};

onMounted(() => {
  events.active.forEach((event) => {
    focushelperel.value.parentElement.addEventListener(
      event,
      onParentMouseEnter
    );
  });
  events.inactive.forEach((event) => {
    focushelperel.value.parentElement.addEventListener(
      event,
      onPrentMouseLeave
    );
  });
});
onBeforeUnmount(() => {
  events.active.forEach((event) => {
    focushelperel.value.parentElement.removeEventListener(
      event,
      onParentMouseEnter
    );
  });
  events.inactive.forEach((event) => {
    focushelperel.value.parentElement.removeEventListener(
      event,
      onPrentMouseLeave
    );
  });
});
</script>
