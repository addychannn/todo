<template>
  <div
    v-if="visible"
    class="pointer-events-none fixed -inset-x-2 top-0 z-[9999]"
  >
    <ProgressBar
      :value="progress"
      class="!overflow-visible !rounded-none bg-gray-400 !bg-opacity-5 leading-none"
      hideLabel
    >
      <template #bar="{ value }">
        <div
          class="absolute inset-y-0 left-0 flex bg-negative leading-none shadow-md shadow-negative transition-all duration-500"
          :style="{ width: `${value}% !important` }"
        >
          <div class="relative h-full w-full">
            <div
              class="absolute right-0 top-1/2 aspect-video h-[150%] -translate-y-1/2 translate-x-1/2 animate-ping rounded-full bg-negative shadow-md shadow-negative"
            ></div>
            <div
              class="absolute right-0 top-1/2 aspect-video h-[150%] -translate-y-1/2 translate-x-1/2 rounded-full bg-negative shadow-md shadow-negative"
            ></div>
          </div>
        </div>
      </template>
    </ProgressBar>
  </div>
</template>

<script setup>
import { defineAsyncComponent, onMounted, ref, watch } from "vue";
import { useSystemStore } from "@/stores";

const ProgressBar = defineAsyncComponent(() =>
  import("@/components/tProgress.vue")
);

const systemStore = useSystemStore();
const progress = ref(0);
const timer = ref(null);
const visible = ref(true);

watch(
  () => systemStore.isLoading,
  (val) => {
    if (val) {
      visible.value = true;
      progress.value = 77;
    } else {
      clearTimeout(timer.value);
      progress.value = 100;
      timer.value = setTimeout(() => {
        visible.value = false;
        progress.value = 0;
        clearTimeout(timer.value);
      }, 600);
    }
  }
);

watch(visible, (val) => {
  if (!val) {
    setTimeout(() => {
      visible.value = true;
    }, 500);
  }
});
</script>
