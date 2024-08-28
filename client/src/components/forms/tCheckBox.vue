<template>
  <div
    v-bind="attr"
    role="checkbox"
    class="inline-flex select-none items-center gap-1 leading-none outline-0"
    :aria-label="name || label || 'checkbox'"
    @click="toggle"
    @keyup.space="toggle"
    @mouseenter="onFocus(!disabled)"
    @focus="onFocus(!disabled)"
    @blur="onFocus(false)"
    @mouseleave="onFocus(false)"
  >
    <div
      class="pointer-events-none relative flex aspect-square h-6 w-6 items-center justify-center rounded-full before:absolute before:inset-0 before:origin-center before:scale-0 before:transform-gpu before:rounded-full before:bg-current before:opacity-[0.12] before:transition before:content-[''] before:dark:opacity-[0.32]"
      :class="[focused && 'before:scale-110', checked && 'before:!bg-primary']"
      aria-hidden="true"
    >
      <input
        type="checkbox"
        class="hidden"
        :id="id"
        :checked="checked"
        :disabled="disabled"
        :indeterminate="indeterminate"
        :name="name"
      />
      <span class="no-outline" tabindex="-1"></span>
      <div
        class="absolute left-1/2 top-1/2 flex -translate-x-1/2 -translate-y-1/2 items-center justify-center"
      >
        <TIcon
          :name="iconName"
          :class="iconClass"
          :size="iconSize"
          :type="iconType"
        />
      </div>
    </div>
    <template v-if="!!slot.label || !!label">
      <div class="flex-auto leading-tight">
        <slot>
          {{ label }}
        </slot>
      </div>
    </template>
  </div>
</template>

<script setup>
import { computed, onMounted, ref, useSlots, watch } from "vue";
import { Helpers } from "@/scripts";

const slot = useSlots();
const props = defineProps({
  modelValue: {
    required: false,
  },
  label: {
    type: String,
    default: "",
  },
  name: {
    type: String,
    default: "",
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  trueValue: {
    default: true,
  },
  falseValue: {
    default: false,
  },
  indeterminateValue: {
    default: undefined,
  },
  toggleIndeterminate: {
    type: Boolean,
    default: false,
  },
  iconSize: {
    type: String,
    default: "md",
    validator: (val) =>
      ["xs", "sm", "md", "lg", "xl", "2xl", "3xl", "4xl", "5xl"].indexOf(
        val.toLowerCase()
      ) > -1,
  },
  iconType: {
    type: String,
    default: "outlined",
    validator: (val) =>
      ["filled", "outlined", "round", "sharp", "two-tone"].indexOf(
        val.toLowerCase()
      ) > -1,
  },
  iconClass: {
    type: String,
    default: "",
  },

  checkedIcon: {
    type: String,
    default: "check_box",
  },
  uncheckedIcon: {
    type: String,
    default: "check_box_outline_blank",
  },
  indeterminateIcon: {
    type: String,
    default: "indeterminate_check_box",
  },
});

const emit = defineEmits(["update:modelValue"]);

const toggleIndex = ref(0);
const focused = ref(false);
const isChecked = ref(props.falseValue);
const id = ref(Helpers.uniqid());

const _modelValue = computed(() => props.modelValue);
const data = computed({
  get: () => isChecked.value,
  set: (val) => {
    isChecked.value = val;
    emit("update:modelValue", val);
  },
});

const indeterminate = computed(
  () => isChecked.value === props.indeterminateValue
);
const checked = computed(() => isChecked.value === props.trueValue);
const stateOrder = computed(() => {
  let order = ["t", "f"];
  if (props.toggleIndeterminate) {
    order.push("i");
  }
  return order;
});
const currentIndex = computed(() => {
  let index = stateOrder.value.indexOf("f");
  if (checked.value) {
    index = stateOrder.value.indexOf("t");
  } else if (indeterminate.value) {
    index = stateOrder.value.indexOf("i");
  }
  return index;
});

const states = computed(() => ({
  t: props.trueValue,
  f: props.falseValue,
  i: props.indeterminateValue,
}));

const attr = computed(() => ({
  for: id.value,
  disabled: props.disabled,
  role: "checkbox",
  "aria-checked": checked.value,
  tabindex: props.disabled ? -1 : 0,
  class: [
    props.disabled && "cursor-not-allowed text-gray-400",
    !props.disabled && "cursor-pointer",
  ],
}));

const iconName = computed(() => {
  let name = checked.value ? props.checkedIcon : props.uncheckedIcon;
  if (indeterminate.value) {
    name = props.indeterminateIcon;
  }
  return name;
});

const toggle = () => {
  toggleStates();
};

const toggleStates = () => {
  if (!props.disabled) {
    toggleIndex.value++;
    let stateCount = stateOrder.value.length - 1;
    if (toggleIndex.value > stateCount) toggleIndex.value = 0;
    let _state = stateOrder.value[toggleIndex.value];
    data.value = states.value[_state];
  }
};

const onFocus = (isFocused = true) => {
  focused.value = isFocused;
};

watch(_modelValue, (val) => {
  isChecked.value = val;
  toggleIndex.value = currentIndex.value;
});

onMounted(() => {
  data.value = props.modelValue;
  toggleIndex.value = currentIndex.value;
});

defineExpose({
  toggle,
});
</script>
