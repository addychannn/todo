<template>
  <button
    class="flex min-h-[1rem] items-center justify-center gap-2 overflow-visible px-2 py-1 outline-none"
    role="switch"
    :aria-checked="modelValue"
    @click="toggleModel"
  >
    <span v-if="!!name" class="sr-only">{{ name }}</span>
    <div
      class="pointer-events-none relative h-3 w-5 select-none rounded-full border bg-opacity-50"
      :class="[
        modelValue && 'border-primary bg-primary text-light',
        !modelValue && ' border-gray-500 bg-gray-500',
      ]"
    >
      <div
        class="absolute top-1/2 aspect-square h-4 w-4 -translate-y-1/2 rounded-full leading-none transition-all"
        :class="[
          modelValue && 'right-0 translate-x-1/2 border-primary bg-primary ',
          !modelValue && 'left-0 -translate-x-1/2 border-gray-500 bg-gray-500',
        ]"
      >
        <div
          v-if="!!iconName"
          class="flex h-full w-full items-center justify-center leading-none"
        >
          <TIcon :name="iconName" size="xs" />
        </div>
      </div>
    </div>
    <div v-if="!!label" class="">
      {{ label }}
    </div>
  </button>
</template>

<script setup>
import { computed, ref } from "vue";
const props = defineProps({
  modelValue: Boolean,
  name: {
    type: String,
    default: "switch button",
  },
  label: {
    type: String,
    default: null,
  },
  onIcon: {
    type: String,
    default: "check",
  },
  offIcon: {
    type: String,
    default: null,
  },
});

const emit = defineEmits(["update:modelValue"]);

const toggleModel = () => {
  emit("update:modelValue", !props.modelValue);
};

const iconName = computed(() => {
  return !props.modelValue ? props.offIcon : props.onIcon;
});
</script>
