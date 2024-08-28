<template>
  <TFieldWrapper>
    <template v-for="(index, name) in slots" v-slot:[name]="data">
      <slot :name="name" v-bind="data"></slot>
    </template>
    <template #default="{ classNames, attr }">
      <textarea
        v-model="data"
        v-bind="attr"
        :rows="`${rows}`"
        :cols="`${cols}`"
        :class="classNames"
        class="TScroll scrollbar-w-4 leading-normal"
      />
    </template>
  </TFieldWrapper>
</template>

<script setup>
import { computed, useSlots } from "vue";

const props = defineProps({
  modelValue: [String, Number],
  rows: {
    type: [String, Number],
    default: "3",
  },
  cols: {
    type: [String, Number],
    default: "20",
  },
});

const emit = defineEmits(["update:modelValue"]);

const slots = useSlots();

const data = computed({
  get: () => props.modelValue,
  set: (val) => emit("update:modelValue", val),
});
</script>
