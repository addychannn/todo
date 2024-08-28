<template>
  <div
    class="relative flex aspect-square items-center justify-center rounded-full bg-inherit bg-[conic-gradient(var(--tw-gradient-stops)_var(--progress),_#FFF0_0deg)] transition-all before:absolute before:left-1/2 before:top-1/2 before:!aspect-square before:w-[85%] before:-translate-x-1/2 before:-translate-y-1/2 before:rounded-full before:bg-inherit before:content-['_']"
    :style="{ '--progress': `${value * 3.6}deg` }"
  >
    <div class="absolute inset-0 flex items-center justify-center">
      <slot>
        <span class="font-semibold">{{ value }}</span>
      </slot>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
const props = defineProps({
  value: Number,
});
</script>
