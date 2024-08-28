<template>
  <div class="flex select-none flex-wrap items-center">
    <div
      ref="container"
      class="basis-full select-text overflow-hidden border-blue-300 transition-all duration-300"
      :class="[open && '!max-h-[100dvh]', !open && 'max-h-0']"
    >
      <template v-if="show">
        <slot></slot>
      </template>
    </div>
  </div>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from "vue";

const props = defineProps({
  modelValue: Boolean,
});
const emits = defineEmits(["update:modelValue"]);

const container = ref(null);
const isOpen = ref(false);
const transitioning = ref(false);

const open = computed({
  get: () => {
    return props.modelValue;
  },
  set: (val) => {
    emits("update:modelValue", val);
  },
});

const show = computed(() => transitioning.value || open.value);

const onTransitionEnd = (e) => {
  transitioning.value = false;
};

watch(open, (val) => {
  transitioning.value = true;
});

onMounted(() => {
  container.value.addEventListener("transitionend", onTransitionEnd);
});

onBeforeUnmount(() => {
  container.value.removeEventListener("transitionend", onTransitionEnd);
});
</script>
