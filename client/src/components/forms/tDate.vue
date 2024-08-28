<template>
  <DatePicker
    v-model="data"
    v-bind="_attr"
    :readonly="readonly"
    :disabled="disabled"
    six-weeks
    teleport="body"
    ref="dp"
  >
    <template
      #dp-input="{
        value,
        onInput,
        onEnter,
        onTab,
        onClear,
        onBlur,
        onKeypress,
        onPaste,
      }"
    >
      <TFieldWrapper
        :label="label"
        :id="id"
        :hint="hint"
        :error="error"
        :errorMessage="errorMessage"
        :readonly="readonly"
        :disabled="disabled"
        :innerClass="innerClass"
      >
        <template v-for="(index, name) in slots" v-slot:[name]="data">
          <slot :name="name" v-bind="data"></slot>
        </template>
        <template #default="{ classNames, attr }">
          <input
            v-bind="attr"
            :class="classNames"
            :value="value"
            class="h-[22px]"
            type="text"
            @input="onInput"
            @keydown.enter="onEnter"
            @keydown.tab="onTab"
            @keypress="onKeypress"
            @paste="onPaste"
            @blur="onBlur"
          />
        </template>
        <template #append>
          <template v-if="clearable && !!data">
            <TIcon
              name="close"
              size="sm"
              class="cursor-pointer text-gray-500"
              @click.prevent="onClear"
            />
          </template>
          <template v-else>
            <TIcon :name="icon" size="sm" class="text-gray-500" />
          </template>
        </template>
      </TFieldWrapper>
    </template>
  </DatePicker>
</template>

<script setup>
import { computed, defineAsyncComponent, useSlots, ref, onMounted } from "vue";
import dayjs from "dayjs";

const DatePicker = defineAsyncComponent(() => import("@vuepic/vue-datepicker"));

const slots = useSlots();
const props = defineProps({
  modelValue: [Object, String],
  id: {
    type: String,
    default: null,
  },
  label: {
    type: String,
    default: null,
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
  clearable: {
    type: Boolean,
    default: false,
  },
  readonly: {
    type: Boolean,
    default: false,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  innerClass: {
    type: String,
    default: "",
  },
  transformFn: {
    type: Function,
    default: null,
  },
  icon: {
    type: String,
    default: "calendar_month",
  },
  type: {
    type: String,
    default: "date",
    validate: (val) => ["date", "datetime", "time"].indexOf(val) > -1,
  },
});

const emit = defineEmits(["update:modelValue"]);

const dp = ref(null);

const data = computed({
  get: () => props.modelValue,
  set: (val) => emit("update:modelValue", transform(val)),
});

const defaultAttr = {
  clearable: false,
  "text-input": {
    format: "MM/dd/yyyy",
  },
  "auto-apply": true,
  "week-start": 0,
  flow: ["year", "month", "calendar"],
};

const _attr = computed(() =>
  Object.assign(defaultAttr, {
    "enable-time-picker": ["time", "datetime"].indexOf(props.type) > -1,
    "min-date":
      ["datetime", "date"].indexOf(props.type) > -1 ? props.min : null,
    "max-date":
      ["datetime", "date"].indexOf(props.type) > -1 ? props.max : null,
    "min-time": props.type == "time" ? props.min : null,
    "max-time": props.type == "time" ? props.max : null,
    "time-picker": props.type == "time",
  })
);

const transform = (val) => {
  return props.transformFn ? props.transformFn(val) : val;
};

defineExpose({ dp });
</script>
