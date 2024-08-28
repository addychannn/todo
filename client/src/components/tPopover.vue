<template>
  <Popover class="flex items-center justify-center leading-none">
    <Float
      :placement="placement"
      :offset="offset || (arrow ? 10 : offset)"
      :shift="6"
      :flip="10"
      :arrow="5"
      :show="visible"
      portal
      enter="transition duration-200 ease-out"
      enter-from="opacity-0 -translate-y-1"
      enter-to="opacity-100 translate-y-0"
      leave="transition duration-150 ease-in"
      leave-from="opacity-100 translate-y-0"
      leave-to="opacity-0 -translate-y-1"
    >
      <TButton
        class="relative overflow-hidden leading-none !outline-none outline-offset-0 focus:outline-primary"
        :class="[..._btnClass, visible && 'pointer-events-none']"
        :disabled="disabled"
        :focusDisabled="focusDisabled"
        :ripple="ripple"
        @click.stop="_toggle()"
      >
        <slot name="button">
          <div
            class="pointer-events-none flex flex-auto items-center justify-center gap-1 font-semibold leading-none"
            :class="_contentClass"
          >
            <TIcon v-if="!!icon" :name="icon" :size="iconSize" />
            <span v-if="!!label" class="leading-none">
              {{ label }}
            </span>
          </div>
        </slot>
      </TButton>

      <PopoverPanel
        static
        class="relative rounded-md border border-foreground/25 bg-background text-foreground shadow-lg focus:outline-none"
        :style="{
          width: `${contentSize.width + 1.5}px`,
          height: `${contentSize.height + 1.5}px`,
        }"
      >
        <FloatArrow
          v-if="arrow"
          class="absolute h-5 w-5 rotate-45 border border-foreground/25 bg-background text-foreground"
        />
        <div
          class="absolute left-0 top-0 overflow-hidden rounded-md bg-background"
          v-on-click-outside="onOutsideClick"
        >
          <slot :close="() => _close()" :visible="visible"> </slot>
          <SizeObserver @resize="onResize" />
        </div>
      </PopoverPanel>
    </Float>
  </Popover>
</template>
<script setup>
import { ref, computed, watch } from "vue";
import { Popover, PopoverButton, PopoverPanel } from "@headlessui/vue";
import { Float, FloatArrow } from "@headlessui-float/vue";
import { vOnClickOutside } from "@vueuse/components";
const props = defineProps({
  label: {
    type: String,
    default: "",
  },
  icon: {
    type: String,
    default: null,
  },
  shown: {
    type: Boolean,
    default: false,
  },
  iconSize: {
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
  btnClass: {
    type: [String, Array],
    default: () => [],
  },
  contentClass: {
    type: [String, Array],
    default: () => [],
  },
  focusClass: {
    type: String,
    default: "bg-foreground",
  },
  focusDisabled: {
    type: Boolean,
    default: false,
  },
  ripple: {
    type: Object,
    default: () => ({}),
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  closeOnOutsideClick: {
    type: Boolean,
    default: true,
  },

  placement: {
    type: String,
    default: "bottom-end",
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
    default: 5,
  },

  arrow: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(["clickedOutside", "update:shown"]);

const visible = ref(false);
const contentSize = ref({ width: 0, height: 0 });

const shown = computed(() => props.shown);

const _contentClass = computed(() =>
  Array.isArray(props.contentClass) ? props.contentClass : [props.contentClass]
);

const _btnClass = computed(() =>
  Array.isArray(props.btnClass) ? props.btnClass : [props.btnClass]
);

const _toggle = () => {
  visible.value = !visible.value;
  emit("update:shown", visible.value);
};

const _show = () => {
  visible.value = true;
  emit("update:shown", visible.value);
};

const onOutsideClick = (ev) => {
  if (props.closeOnOutsideClick) {
    _close();
  }
  emit("clickedOutside", ev);
};

const _close = () => {
  visible.value = false;
  emit("update:shown", visible.value);
};

const onResize = (size) => {
  contentSize.value = {
    width: size.width,
    height: size.height,
  };
};

watch(shown, (val) => {
  visible.value = val;
});

defineExpose({
  close,
});
</script>
