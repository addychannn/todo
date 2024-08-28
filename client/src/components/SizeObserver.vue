<template>
  <component :is="_tag" ref="observe">
    <slot :size="size"></slot>
  </component>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, computed } from "vue";
import wrapper from "./wrapper.vue";

const props = defineProps({
  tag: {
    type: String,
    default: "template",
  },
});
const emit = defineEmits(["resize"]);

const observe = ref(null);
const size = ref({});
let ro = new ResizeObserver(onResize);

const _tag = computed(() => (props.tag == "template" ? wrapper : props.tag));

function onResize() {
  let parentEl = observe.value.$el.parentElement;
  let rect = parentEl.getBoundingClientRect();
  size.value = Object.assign(
    {},
    {
      top: rect.top,
      right: rect.right,
      bottom: rect.bottom,
      left: rect.left,
      width: rect.width,
      height: rect.height,
      x: rect.x,
      y: rect.y,
    },
    {
      offsetHeight: parentEl.offsetHeight,
      offsetWidth: parentEl.offsetWidth,
      scrollHeight: parentEl.scrollHeight,
      scrollWidth: parentEl.scrollWidth,
    }
  );
  emit("resize", size.value);
}

onMounted(() => {
  ro = new ResizeObserver(onResize);
  ro.observe(observe.value.$el.parentElement);
});
onBeforeUnmount(() => {
  ro.unobserve(observe.value.$el.parentElement);
});
</script>
