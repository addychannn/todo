<template>
  <component
    :is="!!to ? 'router-link' : tag"
    class="relative overflow-hidden leading-none !outline-none outline-offset-0 focus:outline-primary"
    :class="[
      disabled &&
        'cursor-not-allowed !text-gray-400 after:absolute after:inset-0 after:bg-gray-500 after:bg-opacity-25',
      !!to && 'block',
    ]"
    v-ripple="ripple"
    v-bind="Object.assign({}, $attrs, attrs)"
    :disabled="disabled"
  >
    <FocusHelper :color="focusClass" :disabled="disabled || focusDisabled" />
    <slot>
      <div
        class="pointer-events-none flex flex-auto items-center justify-center font-semibold leading-none"
        :class="_contentClass"
      >
        <TIcon
          v-if="!!icon"
          :name="icon"
          :size="iconSize"
          :class="[!!label && 'mr-1', ..._iconClass]"
        />
        <span v-if="!!label" class="uppercase leading-none">
          {{ `${label}` }}
        </span>
      </div>
    </slot>
  </component>
</template>

<script setup>
import { computed, useSlots } from "vue";
import { useRouter } from "vue-router";

const slots = useSlots();
const router = useRouter();

const props = defineProps({
  tag: {
    type: String,
    default: "button",
  },
  label: {
    type: [String, Number],
    default: "",
  },
  icon: {
    type: String,
    default: null,
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
  iconClass: {
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
  to: {
    type: Object,
    default: null,
  },
});

const attrs = computed(() => {
  let _attrs = {};
  if (!!props.to) {
    Object.assign(_attrs, { to: props.to });
  }
  return _attrs;
});

const _contentClass = computed(() =>
  Array.isArray(props.contentClass) ? props.contentClass : [props.contentClass]
);

const _iconClass = computed(() =>
  Array.isArray(props.iconClass) ? props.iconClass : [props.iconClass]
);
</script>
