<template>
  <teleport to="body" :disabled="!teleport || contain">
    <transition
      v-bind="_transition"
      @before-leave="transitioning = true"
      @after-enter="transitioning = false"
      @after-leave="transitioning = false"
    >
      <component
        :is="tag"
        v-show="open"
        class="relative !z-[1027] transform rounded-lg border border-foreground/25 bg-background px-2 py-1 text-foreground drop-shadow-md"
        :class="[(!pointerEvents || transitioning) && 'pointer-events-none']"
        ref="floating"
        :style="floatingStyles"
        aria-hidden="true"
        v-bind="$attrs"
        @mouseenter="showTip"
        @mouseleave="hideTip"
        @click="hideOnClick && hideTip()"
      >
        <template v-if="open || (!open && transitioning)">
          <slot :close="() => hideTip" />
        </template>
        <div
          v-if="arrow"
          class="absolute h-2 w-2 border-l border-t border-inherit bg-inherit"
          :class="arrowClass"
          ref="arrowRef"
        />
      </component>
    </transition>
  </teleport>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from "vue";
import {
  useFloating,
  offset,
  flip,
  shift,
  autoUpdate,
  arrow,
} from "@floating-ui/vue";
import { transitions } from "@/scripts";

const props = defineProps({
  tag: {
    type: String,
    default: "div",
  },
  position: {
    type: String,
    default: "bottom",
    validator: (val) =>
      [
        "left",
        "top",
        "right",
        "bottom",

        "left-start",
        "top-start",
        "right-start",
        "bottom-start",

        "left-end",
        "top-end",
        "right-end",
        "bottom-end",
      ].indexOf(val) > -1,
  },
  offset: {
    type: Number,
    default: 8,
  },
  arrow: {
    type: Boolean,
    default: false,
  },
  // Contain in immediate parent instead of moving the tooltip in the body
  contain: {
    type: Boolean,
    default: false,
  },
  pointerEvents: {
    type: Boolean,
    default: false,
  },
  delay: {
    type: Number,
    default: 350, // milliseconds
  },
  hideDelay: {
    type: Number,
    default: 150, // milliseconds
  },
  hideOnClick: {
    type: Boolean,
    default: true,
  },
  showEvents: {
    type: Object,
    default: ["mouseenter", "focus"],
  },
  hideEvents: {
    type: Object,
    default: ["mouseleave", "blur"],
  },
  transitionName: {
    type: String,
    default: "opacity",
    validator: (val) => transitions._names.indexOf(val) > -1,
  },
});

const floating = ref(null);
const open = ref(false);
const arrowRef = ref();
const teleport = ref(false);
const transitioning = ref(false);

const hideTimeOut = ref(null);
const showTimeOut = ref(null);

const reference = computed(() => floating.value?.parentElement);

const { x, y, strategy, isPositioned, middlewareData, placement } = useFloating(
  reference,
  floating,
  {
    open,
    placement: props.position,
    transform: false,
    strategy: "fixed",
    middleware: [
      offset(props.offset),
      flip(),
      shift({ padding: 5 }),
      arrow({
        element: arrowRef,
        padding: props.position.split("-").length > 1 ? 7 : 1,
      }),
    ],
    whileElementsMounted: (...args) => {
      const cleanup = autoUpdate(...args, { animationFrame: true });
      // Important! Always return the cleanup function.
      return cleanup;
    },
  }
);

const floatingStyles = computed(() => ({
  left: `${x.value}px`,
  top: `${y.value}px`,
  position: strategy.value,
}));

const arrowClass = computed(() => {
  let classes = [];
  if (!!middlewareData.value?.arrow) {
    const { x: arrowX, y: arrowY } = middlewareData.value.arrow;
    const opposedSide = {
      left: "right",
      right: "left",
      top: "bottom",
      bottom: "top",
    }[placement.value.split("-")[0]];

    Object.assign(arrowRef.value.style, {
      left: arrowX ? `${arrowX}px` : "",
      top: arrowY ? `${arrowY}px` : "",
      right: ``,
      bottom: ``,
      [opposedSide]: "-4px",
    });

    const rotation = {
      bottom: "-rotate-[135deg]",
      top: "rotate-45",
      left: "-rotate-45",
      right: "rotate-[135deg]",
    };

    classes = rotation[opposedSide];
  }
  return classes;
});

const _transition = computed(() =>
  Object.assign({}, transitions[props.transitionName], {
    enterActiveClass: "transition duration-300",
    leaveActiveClass: "transition duration-300",
  })
);

const showTip = () => {
  if (!!hideTimeOut.value) {
    clearInterval(hideTimeOut.value);
  }
  if (!!showTimeOut.value) {
    clearInterval(showTimeOut.value);
  }
  showTimeOut.value = setInterval(() => {
    open.value = true;
    clearInterval(showTimeOut.value);
  }, props.delay);
};

const hideTip = () => {
  if (!!hideTimeOut.value) {
    clearInterval(hideTimeOut.value);
  }
  if (!!showTimeOut.value) {
    clearInterval(showTimeOut.value);
  }
  hideTimeOut.value = setInterval(() => {
    open.value = false;
    clearInterval(hideTimeOut.value);
  }, props.hideDelay);
};

watch(isPositioned, (isPositioned) => {
  if (isPositioned) {
  }
});

onMounted(() => {
  props.showEvents.forEach((event) => {
    reference.value?.addEventListener(event, showTip);
  });
  props.hideEvents.forEach((event) => {
    reference.value?.addEventListener(event, hideTip);
  });
  teleport.value = true;
});

onBeforeUnmount(() => {
  props.hideEvents.forEach((event) => {
    reference.value?.removeEventListener(event, showTip);
  });
  props.showEvents.forEach((event) => {
    reference.value?.removeEventListener(event, hideTip);
  });
});
</script>

<script>
import { defineComponent } from "vue";

export default defineComponent({
  name: "TToolTip",
  inheritAttrs: false,
});
</script>
