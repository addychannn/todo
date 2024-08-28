<template>
  <transition
    enter-from-class="opacity-0"
    leave-to-class="opacity-0"
    enter-active-class="transition duration-300"
    leave-active-class="transition duration-300"
    @before-leave="transitioning = true"
    @after-enter="transitioning = false"
  >
    <div
      v-show="active"
      ref="el"
      tabindex="0"
      class="inset-0 z-[9999] flex flex-col items-center justify-center bg-background/50 text-foreground outline-none"
      :class="[isFullScreen && 'fixed', !isFullScreen && 'absolute']"
      :style="isFullScreen ? [] : styles"
      @keydown.tab.prevent.stop="
        () => {
          if (active && lockFocus) {
            el?.focus();
          }
        }
      "
      @blur="
        () => {
          if (active && lockFocus) {
            el?.focus();
          }
        }
      "
    >
      <template v-if="active">
        <slot>
          <TSpinnerOrbit class="h-8 w-8" />
          <div v-if="text" class="italic">{{ text }}</div>
        </slot>
      </template>
    </div>
  </transition>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch, nextTick } from "vue";
import { useSystemStore } from "@/stores";
const props = defineProps({
  text: {
    type: String,
    default: null,
  },
  active: {
    type: Boolean,
    default: false,
  },
  isFullScreen: {
    type: Boolean,
    default: false,
  },
  lockFocus: {
    type: Boolean,
    default: false,
  },
});

let parentStyle = reactive({});
let styles = reactive({
  "border-top-left-radius": 0,
  "border-top-right-radius": 0,
  "border-bottom-left-radius": 0,
  "border-bottom-right-radius": 0,
});

const systemStore = useSystemStore();
const el = ref(null);
const transitioning = ref(null);

const canFocus = computed(() => !transitioning.value && props.active);

function getParentStyle() {
  return window.getComputedStyle(el.value.parentElement);
}

const setStyle = () => {
  styles["border-top-left-radius"] = parentStyle.borderTopLeftRadius ?? 0;
  styles["border-top-right-radius"] = parentStyle.borderTopRightRadius ?? 0;
  styles["border-bottom-left-radius"] = parentStyle.borderBottomLeftRadius ?? 0;
  styles["border-bottom-right-radius"] =
    parentStyle.borderBottomRightRadius ?? 0;
};

watch(
  () => props.active,
  (newValue, oldValue) => {
    if (newValue) {
      parentStyle = getParentStyle();
      setStyle();
      if (
        el.value.parentElement.nodeName != "BODY" &&
        parentStyle.position != "relative"
      ) {
        if (import.meta.env.MODE != "production") {
          console.trace('Parent element must have a position of "relative"!');
        }
      }
    }
  }
);

watch(transitioning, (val) => {
  if (!val && props.lockFocus) {
    nextTick(() => {
      el.value?.focus();
    });
  }
});
onMounted(() => {
  parentStyle = getParentStyle();
});
</script>
