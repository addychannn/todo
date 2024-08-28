<template>
  <component
    :is="tag"
    v-bind="wrapperAttr"
    class="group/outer relative flex flex-nowrap items-start leading-none"
    :class="[
      (error != null || hint != null) && 'mb-5',
      disabled && '!cursor-not-allowed',
    ]"
  >
    <SizeObserver v-if="!!onResize" @resize="onResize" />
    <div v-if="!!slots.before" class="flex items-center self-stretch pr-1.5">
      <slot name="before" :error="error"></slot>
    </div>
    <div
      class="group/inner relative box-border flex flex-auto gap-1 rounded-lg border border-gray-500/25 px-2 shadow-md outline-2 outline-primary transition-all"
      :class="[
        ..._innerClass,
        cursor,
        error && '!border-negative outline-negative',
        !readonly && 'focus-within:outline',
        readonly && 'border-dashed border-gray-700 focus-within:border-primary',
        disabled &&
          'pointer-events-none !cursor-not-allowed select-none !bg-gray-300',
      ]"
      :style="innerStyle"
    >
      <div v-if="!!slots.prepend" class="flex items-center self-stretch pr-1.5">
        <slot name="prepend" :error="error"></slot>
      </div>
      <div
        class="relative flex-auto pb-2 pt-2 leading-none"
        :class="[!!label && '!pb-1 !pt-1.5']"
      >
        <slot
          :attr="attr"
          :classNames="[
            'peer w-full bg-inherit placeholder-transparent outline-none placeholder:select-none',
            !!label && 'mt-1.5',
          ]"
          :error="error"
        ></slot>
        <div
          v-if="!!label || !!slots.label"
          class="pointer-events-none absolute left-0 top-[0.625rem] origin-top-left -translate-y-[45%] scale-[0.65] select-none text-lg leading-none transition-all duration-300 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100 peer-focus:-translate-y-[45%] peer-focus:scale-[0.65]"
        >
          <div
            class="font-medium leading-none group-focus-within/inner:text-primary"
            :class="[
              error && 'animate-shake !text-negative',
              disabled && 'text-gray-400',
            ]"
          >
            <slot name="label" :error="error">
              {{ label }}
            </slot>
          </div>
        </div>
      </div>
      <div
        v-if="!!slots.append || error"
        class="flex items-center gap-1 self-stretch"
      >
        <slot name="append" :error="error"></slot>
        <slot name="error" :error="error">
          <TIcon v-if="error" name="error" class="text-negative" />
        </slot>
      </div>
      <div
        v-if="(error != null && errorMessage != null) || hint != null"
        class="absolute inset-x-0 bottom-0 translate-y-full px-2 pt-1 text-[11px] leading-none"
      >
        <transition
          enterFromClass="opacity-0 -translate-y-full"
          leaveToClass="opacity-0 -translate-y-full"
          enterActiveClass="transition duration-300"
          leaveActiveClass="transition duration-300"
        >
          <div
            v-if="error"
            class="absolute left-2 right-0 top-0.5 mt-0.5 text-[11px] font-semibold leading-none"
          >
            <slot
              name="errorMessage"
              :error="error"
              :errorMessage="errorMessage"
            >
              <span
                class="rounded-full border-negative px-1 leading-tight text-negative dark:border dark:bg-negative/75 dark:text-light"
              >
                {{ errorMessage }}
              </span>
            </slot>
          </div>
          <div
            v-else
            class="absolute inset-x-2 top-1 text-[11px] leading-none text-gray-400"
          >
            <slot name="hint" :hint="hint">
              <div :class="_hintClass">
                {{ hint }}
              </div>
            </slot>
          </div>
        </transition>
      </div>
    </div>
    <div v-if="!!slots.after" class="flex items-center self-stretch pl-1.5">
      <slot name="after" :error="error"></slot>
    </div>
  </component>
</template>

<script setup>
import { computed, useSlots } from "vue";
import { Helpers } from "@/scripts";

const props = defineProps({
  tag: {
    type: String,
    default: "label",
  },
  id: {
    type: String,
    default: null,
  },
  label: {
    type: String,
    default: null,
  },
  readonly: {
    type: Boolean,
    default: false,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  hint: {
    type: String,
    default: null,
  },
  error: {
    type: Boolean,
    default: null,
  },
  errorMessage: {
    type: String,
    default: null,
  },
  cursor: {
    type: String,
    default: "cursor-text",
    validator: (val) => /^cursor-/.test(val),
  },
  onResize: {
    type: Function,
    default: null,
  },
  innerClass: {
    type: [Object, String],
    default:
      "focus-within:bg-light focus-within:text-dark text-foreground focus-within:duration-700 !shadow-none",
  },
  innerStyle: {
    type: [Object, String],
    default: "",
  },
  hintClass: {
    type: [Object, String],
    default: "text-end leading-none",
  },
});

const slots = useSlots();

const attr = computed(() => {
  let attrs = {
    id: props.id ?? Helpers.uniqid(),
    placeholder: " ",
    "aria-label": props.label,
    readonly: props.readonly,
    disabled: props.disabled,
  };

  return attrs;
});

const wrapperAttr = computed(() => {
  let attrs = {
    "aria-disabled": props.disabled,
    "aria-readonly": props.readonly,
  };
  if (props.tag == "label") {
    Object.assign(attrs, { for: attr.value.id });
  }

  return attrs;
});

const _innerClass = computed(() =>
  Array.isArray(props.innerClass) ? props.innerClass : [props.innerClass]
);

const _hintClass = computed(() =>
  Array.isArray(props.hintClass) ? props.hintClass : [props.hintClass]
);
</script>
