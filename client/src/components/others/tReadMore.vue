<template>
  <div
    :id="elId"
    class="relative"
    :class="[expand && '!line-clamp-none']"
    ref="container"
    :key="elId"
  >
    <slot :expanded="expand"></slot>
    <div
      v-if="inEllipsis"
      class="flex flex-col items-stretch justify-end"
      :class="overlayClass"
    >
      <slot name="readmore" :expanded="expand">
        <div
          class="cursor-pointer select-none text-center text-sm font-semibold h-5"
          @click="expand = !expand"
        >
          Read {{ expand ? "Less" : "More" }}
        </div>
      </slot>
    </div>
    <SizeObserver @resize="onResize" />
  </div>
</template>

<script setup>
import { computed, ref, watch } from "vue";
import { Helpers } from "@/scripts";
const props = defineProps({
  overlayExpandedClass: {
    type: [String, Array],
    default: "rounded-lg bg-foreground/10",
  },
  overlayCollapsedClass: {
    type: [String, Array],
    default: "bg-gradient-to-t from-dark",
  },
});

const container = ref(null);
const expand = ref(false);
const scrollH = ref(0);
const elId = ref(Helpers.uniqid("readmore"));

const heights = computed(() => ({
  offset: container.value?.offsetHeight ?? 0,
  scroll: container.value?.scrollHeight ?? 0,
}));

const overlayClass = computed(() => {
  return expand.value
    ? ["", ..._overlayExpandedClass.value]
    : ["absolute inset-0", ..._overlayCollapsedClass.value];
});

const _overlayExpandedClass = computed(() => {
  return Array.isArray(props.overlayExpandedClass)
    ? props.overlayExpandedClass
    : [props.overlayExpandedClass];
});

const _overlayCollapsedClass = computed(() => {
  return Array.isArray(props.overlayCollapsedClass)
    ? props.overlayCollapsedClass
    : [props.overlayCollapsedClass];
});

const inEllipsis = computed(() => heights.value.offset < heights.value.scroll);

const onResize = (size) => {
  scrollH.value = size.scrollHeight - (expand.value ? 20 : 0);
};

watch(scrollH, (val) => {
  elId.value = Helpers.uniqid("readmore");
});
</script>
