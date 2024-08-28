<template>
  <label
    class="flex items-center pb-5"
    @mouseenter="_hover = true"
    @mouseleave="_hover = false"
  >
    <div
      v-if="_slotExists('before')"
      class="flex items-center justify-center"
      :class="[dense && 'pr-1.5', !dense && 'pr-2']"
    >
      <slot name="before" />
    </div>
    <div class="relative flex grow flex-col">
      <div
        :class="[
          'flex items-center overflow-hidden px-2',
          !outlined && rounded && 'rounded-t-3xl bg-gray-200',
          outlined && !rounded && 'rounded-md',
          outlined && rounded && 'rounded-full !px-4',
          outlined && 'border',
          !outlined && 'border-b',
          !error && _focused && `${borderColor}`,
          !error && _hover && !_focused && 'border-gray-600',
          error && 'border-red-700',
          _hover && 'cursor-text',
          'border-gray-400',
          bg,
        ]"
      >
        <div
          v-if="_slotExists('prepend')"
          class="flex items-center justify-center"
          :class="[dense && 'pr-1.5', !dense && 'pr-2', color]"
        >
          <slot name="prepend" />
        </div>
        <div class="group relative flex grow flex-col">
          <slot
            :fieldClass="[
              dense && !!label && 'pt-3.5 pb-1',
              !dense && !!label && 'pt-5 pb-2',
              !dense && !label && 'py-3',
              dense && !label && 'py-2',
              'peer',
              'grow border-0',
              'bg-transparent',
              'placeholder-transparent placeholder:select-none',
              'outline-none',
              'text-black/[0.87]',
              color,
            ]"
            :placeholder="' '"
            :onFocus="() => onFocus()"
            :onBlur="() => onBlur()"
          />
          <label
            v-if="label"
            :class="[
              dense && 'top-[10px]',
              !dense && 'top-[15px]',
              'transition-all duration-300',
              'origin-top-left scale-75',
              '-translate-y-[30%]',
              'peer-focus:scale-75',
              'peer-focus:-translate-y-[30%]',
              'peer-placeholder-shown:scale-100',
              'peer-placeholder-shown:translate-y-0',
              'absolute left-0 text-sm',
              'pointer-events-none font-normal',
              'ellipsis',
              'select-none ',
            ]"
          >
            <div
              :class="[
                !error && _focused && `${labelColor}`,
                !error && !_focused && 'text-gray-500',
                error && 'animate-shake text-red-700',
                'transition-all duration-300',
              ]"
            >
              {{ label }}
            </div>
          </label>
        </div>
        <div v-if="!outlined">
          <div
            class="transition-all duration-500"
            :class="[
              !error && `${borderColor}`,
              error && 'border-negative',
              _focused && 'scale-x-100',
              _blurred && !error && 'scale-x-0',
            ]"
          />
        </div>
        <div v-if="clearable" class="flex items-center justify-center pl-2">
          <button
            class="flex items-center justify-center rounded-full border-0 p-0 outline-0"
            :class="[color]"
            tabindex="-1"
            @click="
              _data = null;
              $emit('clear');
            "
          >
            <TIcon name="close" size="sm" />
          </button>
        </div>
        <div
          v-if="_slotExists('append') || error || loading"
          class="flex items-center justify-center"
          :class="[dense && 'pl-1.5', !dense && 'pl-2']"
        >
          <template v-if="loading">
            <TIcon
              name="fiber_manual_record"
              class="animate-bounce text-gray-500"
            />
          </template>
          <template v-else>
            <template v-if="error">
              <TIcon name="info" size="md" class="text-negative" />
            </template>
            <template v-else>
              <slot name="append" />
            </template>
          </template>
        </div>
      </div>
      <transition
        enter-from-class="-translate-y-full opacity-0"
        leave-to-class="-translate-y-full opacity-0"
        enter-active-class="transition duration-300"
        leave-active-class="transition duration-300"
      >
        <div
          v-if="error"
          :class="[
            'absolute',
            'left-0',
            'bottom-0',
            'right-0',
            'min-h-[20px]',
            '-mb-4 px-3 pt-2',
            'text-xs',
            'leading-none',
            'text-negative',
            'transition-all',
          ]"
        >
          {{ errorMessage }}
        </div>
      </transition>
    </div>
    <div
      v-if="_slotExists('after')"
      class="flex items-center justify-center"
      :class="[dense && 'pl-1.5', !dense && 'pl-2']"
    >
      <slot name="after" />
    </div>
  </label>
</template>

<script setup>
import { computed, ref, useSlots } from "vue";

const slots = useSlots();

const props = defineProps({
  modelValue: [String, Number],
  debounce: {
    type: Number,
    default: 0,
  },
  label: {
    type: String,
    default: "",
  },
  color: {
    type: String,
    default: "text-slate-900",
  },
  labelColor: {
    type: String,
    default: "text-blue-700",
  },
  borderColor: {
    type: String,
    default: "border-blue-700",
  },
  bg: {
    type: String,
    default: "bg-inherit",
  },
  type: {
    type: String,
    default: "text",
  },
  errorMessage: {
    type: String,
    default: "",
  },
  dense: {
    type: Boolean,
    default: false,
  },
  clearable: {
    type: Boolean,
    default: false,
  },
  loading: {
    type: Boolean,
    default: false,
  },
  outlined: {
    type: Boolean,
    default: false,
  },
  rounded: {
    type: Boolean,
    default: false,
  },
  error: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits([
  "update:modelValue",
  "update:name",
  "focus",
  "blur",
  "clear",
  "hover",
]);

const data = ref();
const isHovering = ref(false);
const isFocused = ref(false);
const isBlurred = ref(true);
const timer = ref(null);

const _data = computed({
  get: () => props.modelValue ?? data.value,
  set: (val) => {
    data.value = val;
    if (timer.value != null) {
      clearTimeout(timer.value);
    }

    if (props.debounce > 0) {
      timer.value = setTimeout(() => {
        emit("update:modelValue", val);
        clearTimeout(timer.value);
      }, props.debounce);
    } else {
      emit("update:modelValue", val);
    }
  },
});

const _hover = computed({
  get: () => isHovering.value,
  set: (val) => {
    isHovering.value = val;
    emit("hover", val);
  },
});

const _focused = computed({
  get: () => isFocused.value,
  set: (val) => {
    isFocused.value = val;
    if (!!val) {
      emit("focus");
    }
  },
});

const _blurred = computed({
  get: () => isBlurred.value,
  set: (val) => {
    isBlurred.value = val;
    if (!!val) {
      emit("blur");
    }
  },
});

const _slotExists = (name) => {
  return slots[name];
};

const onFocus = () => {
  _focused.value = true;
  _blurred.value = false;
};

const onBlur = () => {
  _focused.value = false;
  _blurred.value = true;
};
</script>
