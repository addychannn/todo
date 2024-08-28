<template>
  <component
    :is="tag"
    class="fixed transition-all"
    :class="classNames"
    @click="scrollUp"
  >
    <span class="sr-only">Scroll Up</span>
    <slot>
      <TIcon name="north" size="sm" />
    </slot>
  </component>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from "vue";

const props = defineProps({
  offset: {
    type: Number,
    default: 10,
  },
  target: {
    type: Element,
    default: document.body,
  },
  x: {
    type: Number,
    default: 0,
  },
  y: {
    type: Number,
    default: 0,
  },
  behavior: {
    type: String,
    default: "smooth",
    validator: (val) => ["smooth", "instant", "auto"].indexOf(val) > -1,
  },
  tag: {
    type: String,
    default: "button",
  },
});

const show = ref(false);
const classNames = computed(() => {
  return [
    show.value && "translate-y-0 opacity-1",
    !show.value && "translate-y-[150%] pointer-events-none opacity-0",
  ];
});

const scrollUp = () => {
  let options = {
    top: props.x,
    left: props.y,
    behavior: props.behavior,
  };
  props.target.scrollTo(options);
};

const onScroll = (e) => {
  let scrollYOffset = e.target.scrollTop;
  show.value = scrollYOffset > props.offset;
};

onMounted(() => {
  props.target.addEventListener("scroll", onScroll);
});

onBeforeUnmount(() => {
  props.target.removeEventListener("scroll", onScroll);
});
</script>
