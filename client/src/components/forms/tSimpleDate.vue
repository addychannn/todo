<template>
  <label
    class="relative flex items-start gap-1"
    :class="[
      (error != null || hint != null) && 'mb-5',
      disabled && '!cursor-not-allowed',
    ]"
    :aria-disabled="disabled"
  >
    <TList
      :label="editor.month.name"
      v-model="editor.month.value"
      @update:modelValue="onDateChanged"
      @paste.prevent="onPaste"
      :disabled="disabled"
      :options="months"
      :error="error"
      innerClass="bg-light text-dark"
      class="!mb-0"
    >
      <template #error>
        <span class="hidden"></span>
      </template>
    </TList>
    <TInput
      :label="editor.day.name"
      v-model="editor.day.value"
      @update:modelValue="onDateChanged"
      @paste.prevent="onPaste"
      :disabled="disabled"
      :error="error"
      :min="1"
      :max="32"
      type="number"
      innerClass="bg-light text-dark max-w-[7rem]"
      class="!mb-0"
    >
      <template #error>
        <span></span>
      </template>
    </TInput>
    <TInput
      :label="editor.year.name"
      v-model="editor.year.value"
      @update:modelValue="onDateChanged"
      @paste.prevent="onPaste"
      :disabled="disabled"
      :error="error"
      :maxlength="4"
      :min="0"
      type="number"
      innerClass="bg-light text-dark max-w-[7rem]"
      class="!mb-0"
    >
      <template #error>
        <span></span>
      </template>
    </TInput>
    <div
      v-if="(error != null && errorMessage != null) || _hint != null"
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
          class="absolute inset-x-2 top-0.5 mt-0.5 text-[11px] font-semibold leading-none text-light"
        >
          <slot name="errorMessage" :error="error" :errorMessage="errorMessage">
            <span
              class="rounded-full border border-negative bg-negative/75 px-1 leading-tight"
            >
              {{ errorMessage }}
            </span>
          </slot>
        </div>
        <div
          v-else-if="!!_hint"
          class="absolute inset-x-2 top-1 mt-0.5 text-end text-[11px] font-semibold leading-none text-foreground/50"
        >
          {{ _hint }}
        </div>
      </transition>
    </div>
  </label>
</template>

<script setup>
import { reactive, ref, onMounted, computed, watch } from "vue";
import dayjs from "dayjs";
import objectSupport from "dayjs/plugin/objectSupport";
import { InputField, transitions } from "@/scripts";

dayjs.extend(objectSupport);

const props = defineProps({
  modelValue: String,
  error: {
    type: Boolean,
    default: null,
  },
  errorMessage: {
    type: String,
    default: null,
  },
  format: {
    type: String,
    default: null,
  },
  innerClass: {
    type: [Array, String],
    default: "",
  },
  hint: {
    type: String,
    default: null,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(["update:modelValue", "updating"]);

const months = [
  { label: "January", value: 0 },
  { label: "February", value: 1 },
  { label: "March", value: 2 },
  { label: "April", value: 3 },
  { label: "May", value: 4 },
  { label: "June", value: 5 },
  { label: "July", value: 6 },
  { label: "August", value: 7 },
  { label: "September", value: 8 },
  { label: "October", value: 9 },
  { label: "November", value: 10 },
  { label: "December", value: 11 },
];

const editor = reactive({
  month: new InputField().setName("Month").setRules("required"),
  day: new InputField().setName("Day").setRules("required|max:31|min:1"),
  year: new InputField().setName("Year").setRules("required|min:1900"),
});

const _hint = computed(() =>
  !!props.hint
    ? props.hint
    : !!props.modelValue
    ? dayjs(props.modelValue).format("MMMM DD, YYYY")
    : null
);

const onDateChanged = () => {
  if (isAllFilled() && editor.year.value > 1000) {
    let date = dayjs({
      year: editor.year.value,
      month: editor.month.value,
      day: editor.day.value,
    });
    setEditorDate(date);
    emit("update:modelValue", date.format(props.format));
  } else {
    emit("update:modelValue", null);
  }
  emit("updating", {
    year: editor.year.value,
    month: editor.month.value,
    day: editor.day.value,
  });
};

const isAllFilled = () => {
  return (
    editor.month.value != null &&
    editor.day.value != null &&
    editor.year.value != null
  );
};

const setEditorDate = (date) => {
  editor.year.value = 1 * date.format("YYYY");
  editor.month.value = 1 * date.format("M") - 1;
  editor.day.value = 1 * date.format("D");
};

const parseDate = (data) => {
  if (!!data && dayjs(data).isValid()) {
    let date = dayjs(data);
    setEditorDate(date);
    emit("update:modelValue", date.format(props.format));
    emit("updating", {
      year: editor.year.value,
      month: editor.month.value,
      day: editor.day.value,
    });
  }
};

const onPaste = (evt) => {
  let clipboardData = evt.clipboardData || window.Clipboard;
  let pastedData = clipboardData.getData("Text");
  parseDate(pastedData);
};

watch(
  () => props.modelValue,
  (val) => {
    parseDate(val);
  }
);

onMounted(() => {
  parseDate(props.modelValue);
});
</script>
