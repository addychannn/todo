<template>
  <Listbox v-model="data">
    <Float
      as="div"
      class="relative"
      placement="bottom"
      portal
      :offset="4"
      :flip="10"
      floating-as="template"
      adaptive-width
    >
      <ListboxButton
        class="relative w-full rounded-lg border border-gray-200 bg-white py-2 pl-3.5 pr-10 text-left text-sm leading-5 text-dark shadow-md"
      >
        {{ data?.label ?? "(None)" }}
        <span
          class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2"
        >
          <TIcon name="unfold_more" class="text-gray-400" />
        </span>
        <SizeObserver @resize="onResize" />
      </ListboxButton>

      <ListboxOptions
        class="max-h-96 w-full overflow-x-hidden rounded-md border border-gray-200 bg-white shadow-lg focus:outline-none"
      >
        <ListboxOption
          v-for="option in options"
          v-slot="{ active, selected }"
          :key="`${option.label}_${option.value}`"
          :value="option"
          :disabled="option.disabled"
          as="template"
        >
          <li
            class="relative block h-10 w-full cursor-default px-3 py-2 text-left text-sm text-gray-600"
            :class="{
              'bg-secondary/50': active,
              'bg-secondary font-medium text-light': selected,
              'text-gray-300': option.disabled,
            }"
          >
            <span class="block truncate">
              {{ option.label }}
            </span>
          </li>
        </ListboxOption>
      </ListboxOptions>
    </Float>
  </Listbox>
</template>

<script setup>
import { computed, ref } from "vue";
import {
  Listbox,
  ListboxButton,
  ListboxOption,
  ListboxOptions,
} from "@headlessui/vue";
import { Float } from "@headlessui-float/vue";

const props = defineProps({
  modelValue: Object,
  options: Array,
});

const emit = defineEmits(["update:modelValue"]);

const btnSize = ref({
  width: 0,
  height: 0,
});

const data = computed({
  get: () => props.modelValue,
  set: (val) => emit("update:modelValue", val),
});

const onResize = (size) => {
  btnSize.value.width = size.width;
  btnSize.value.height = size.height;
};
</script>
