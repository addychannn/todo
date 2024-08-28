<template>
  <teleport to="body" :disabled="contain">
    <transition :name="`dialog-${transition}`">
      <component
        v-if="_open"
        :is="'div'"
        class="__dialog__ pointer-events-none absolute inset-0 z-[1030]"
        :class="[!contain && 'z-[1030]', contain && 'z-20']"
        ref="$el"
        v-trap-focus
        @keyup.esc="close(false)"
      >
        <div
          class="__dialog_container__ pointer-events-none relative flex h-full w-full"
          :class="_containerClass"
        >
          <div
            v-if="!seamless"
            class="__dialog_backdrop__ pointer-events-auto absolute inset-0 bg-background-accent bg-opacity-50 transition-opacity"
            @click.stop="close(false)"
          />
          <div
            v-bind="$attrs"
            class="__dialog_wrapper__ pointer-events-auto absolute"
            :class="_wrapperClass"
            ref="$contentWrapper"
            @keyup.esc="close(false)"
          >
            <slot :close="() => close(true)"></slot>
          </div>
        </div>
      </component>
    </transition>
  </teleport>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false,
  },
  contain: {
    type: Boolean,
    default: false,
  },
  persistent: {
    type: Boolean,
    default: false,
  },
  seamless: {
    type: Boolean,
    default: false,
  },
  fullScreen: {
    type: Boolean,
    default: false,
  },
  fullWidth: {
    type: Boolean,
    default: false,
  },
  fullHeight: {
    type: Boolean,
    default: false,
  },

  blur: {
    type: Boolean,
    default: false,
  },
  position: {
    type: String,
    default: "center",
    validator: (val) =>
      [
        "center",
        "top",
        "bottom",
        "left",
        "right",
        "top-left",
        "top-right",
        "bottom-left",
        "bottom-right",
      ].indexOf(val) > -1,
  },

  transition: {
    type: String,
    default: "pop",
    validator: (val) =>
      [
        "pop",
        "slide-left",
        "slide-right",
        "slide-up",
        "slide-down",
        "pop-slide-left",
        "pop-slide-right",
        "pop-slide-up",
        "pop-slide-down",
      ].indexOf(val) > -1,
  },

  wrapperClass: {
    type: [String, Array],
    default: "",
  },
});
const emits = defineEmits(["update:modelValue", "open"]);

const isOpen = ref(false);
const $el = ref(null);
const $contentWrapper = ref(null);

const _open = computed({
  get() {
    return props.modelValue ?? isOpen.value;
  },
  set(val) {
    isOpen.value = val;
    emits("update:modelValue", val);
    if (val && !!$el.value) {
      $contentWrapper.value.focus();
    }
  },
});

const _wrapperClass = computed(() => {
  return [
    strToArray(props.wrapperClass),
    (props.fullScreen || props.fullWidth) && "w-full",
    (props.fullScreen || props.fullHeight) && "h-full",
  ];
});

const _containerClass = computed(() => {
  const _position = props.position;
  return [
    _position == "center" && "justify-center items-center",
    _position == "top" && "justify-center items-start",
    _position == "left" && "justify-start items-center",
    _position == "right" && "justify-end items-center",
    _position == "bottom" && "justify-center items-end",
    _position == "top-left" && "justify-start items-start",
    _position == "top-right" && "justify-end items-start",
    _position == "bottom-left" && "justify-start items-end",
    _position == "bottom-right" && "justify-end items-end",
  ];
});

const _parentEl = computed(() => {
  return $el.value?.parentElement ?? null;
});

const close = (force = true) => {
  if (force || !props.persistent) {
    _open.value = false;
  } else {
    shake();
  }
};

const shake = () => {
  const childEl = $contentWrapper.value?.firstElementChild;
  if (childEl && !childEl.classList.contains("animate-shake")) {
    childEl.classList.add("animate-shake");
    childEl.addEventListener("animationend", (e) => {
      childEl.classList.remove("animate-shake");
    });
  }
};

const strToArray = (args) => {
  return Array.isArray(args) ? args : [args];
};

watch(_parentEl, (newVal) => {
  if (newVal && _open && !props.seamless) {
    $el.value.focus();
    _parentEl.value.classList.add("__dialog_open__");
    if (props.blur) {
      [..._parentEl.value.children].forEach((child) => {
        if (!child.classList.contains("__dialog__")) {
          child.classList.add("__dialog_blur__");
        }
      });
    }
  }
});
watch(_open, (newVal) => {
  if (!newVal && !props.seamless) {
    _parentEl.value.classList.remove("__dialog_open__");
    [..._parentEl.value.children].forEach((child) => {
      if (!child.classList.contains("__dialog__")) {
        child.classList.remove("__dialog_blur__");
      }
    });
  }
});
</script>
