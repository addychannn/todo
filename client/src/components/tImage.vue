<template>
  <figure
    ref="imgFigure"
    v-intersection-observer="onVisible"
    class="relative m-0 flex items-center overflow-hidden"
    :class="[
      (isLoading || error) && 'justify-center',
      circle && 'aspect-square rounded-full',
    ]"
  >
    <template v-if="error">
      <slot name="error">
        <TIcon name="broken_image" size="lg" class="text-gray-400" />
      </slot>
    </template>
    <template v-else>
      <template v-if="isLoading">
        <div></div>
        <slot name="loading">
          <TSpinnerCircles class="h-10 w-10" />
        </slot>
      </template>
      <img v-else :src="imgSrc" :alt="alt" :class="imgClass" />
    </template>
  </figure>
</template>

<script setup>
import { computed, ref } from "vue";
import { vIntersectionObserver } from "@vueuse/components";
import { useImage } from "@vueuse/core";

const imgFigure = ref(null);
const observer = ref(null);
const props = defineProps({
  src: {
    type: String,
    default: "",
  },
  alt: {
    type: String,
    default: "",
  },
  eager: {
    type: Boolean,
    default: false,
  },
  to: {
    type: [String, Object],
    default: null,
  },
  href: {
    type: String,
    default: null,
  },
  hrefTarget: {
    type: String,
    default: "self",
    validator: (val) => {
      return ["blank", "parent", "self", "top"].indexOf(val) > -1;
    },
  },
  fit: {
    type: String,
    default: "object-cover",
    validator: (val) => {
      return (
        [
          "object-fill",
          "object-contain",
          "object-cover",
          "object-none",
          "object-scale-down",
        ].indexOf(val) > -1
      );
    },
  },
  rounded: {
    type: Boolean,
    default: true,
  },
  circle: {
    type: Boolean,
    default: false,
  },
});
const emits = defineEmits(["loading", "error"]);

const imageOpts = computed(() => ({
  src: props.src,
}));

const { isLoading, error } = useImage(imageOpts);

const isVisible = ref(false);
const imgClass = computed(() => [
  props.fit,
  isLoading && "flex-auto",
  "h-full",
]);
const imgSrc = computed(() => (isVisible.value ? props.src : null));

const onVisible = ([{ isIntersecting }]) => {
  isVisible.value = isIntersecting;
};
</script>
