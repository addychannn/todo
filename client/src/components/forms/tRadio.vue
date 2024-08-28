<template>
  <label
    class="group relative cursor-pointer select-none rounded-full py-2 pl-1 pr-2"
    tabindex="0"
    :for="elID"
  >
    <input
      :id="elID"
      type="radio"
      :name="name"
      :value="value"
      v-model="checked"
      class="hidden"
    />
    <div class="pointer-events-none flex items-center gap-2">
      <slot :label="label" :checked="checked">
        <slot
          name="icon"
          :label="label"
          :checked="checked"
          :checkedIcon="checkedIcon"
          :uncheckedIcon="uncheckedIcon"
        >
          <TIcon
            :name="checked == value ? checkedIcon : uncheckedIcon"
            size="sm"
            class="relative aspect-square rounded-full before:absolute before:left-1/2 before:top-1/2 before:h-full before:w-full before:-translate-x-1/2 before:-translate-y-1/2 before:scale-0 before:rounded-full before:bg-foreground/10 before:leading-none before:transition-transform group-hover:before:scale-150"
          />
        </slot>
        <slot name="label" :label="label" :checked="checked">
          <span class="font-semibold leading-none">{{ label }}</span>
        </slot>
      </slot>
    </div>
  </label>
</template>

<script setup>
import { computed, ref } from "vue";
import { Helpers } from "@/scripts";

const props = defineProps({
  modelValue: [Number, String, Boolean, null, undefined],
  value: [Number, String, Boolean, null, undefined],
  name: String,
  label: {
    type: String,
    default: null,
  },
  iconSize: {
    type: String,
    default: "sm",
    validator: (val) =>
      ["xs", "sm", "md", "lg", "xl", "2xl", "3xl", "4xl", "5xl"].indexOf(val) >
      -1,
  },
  checkedIcon: {
    type: String,
    default: "radio_button_checked",
  },
  uncheckedIcon: {
    type: String,
    default: "radio_button_unchecked",
  },
});

const emit = defineEmits(["update:modelValue"]);

const elID = ref(Helpers.uniqid("radio"));

const checked = computed({
  get: () => props.modelValue,
  set: (val) => emit("update:modelValue", val),
});
</script>
