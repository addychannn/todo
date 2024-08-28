<template>
  <MediaDevices :pause="!!imgSrc">
    <template
      #default="{
        stream,
        loading,
        prompting,
        devices,
        cycle,
        capabilities,
        error,
      }"
    >
      <slot> </slot>
      <div v-if="!!error" class="text-negative" :class="_wrapperClass">
        {{ error.message }}
      </div>
      <div v-else-if="!!imgSrc" class="relative" :class="_wrapperClass">
        <TImage :src="imgSrc" class="h-full rounded-md" />
        <div class="absolute right-2 top-2">
          <TButton class="rounded-full p-1" @click="imgFile = null">
            <TIcon name="close" class="text-light drop-shadow" />
          </TButton>
        </div>
      </div>
      <div
        v-else-if="!loading && !prompting"
        class="group relative overflow-hidden"
        :class="_wrapperClass"
      >
        <VideoStream
          :stream="stream"
          :key="stream?.id ?? '_pending'"
          class=""
          :style="{}"
        />
        <GridOverlay v-if="grid" />
        <div
          class="absolute inset-x-0 top-0 flex items-center gap-2 border-b border-dark bg-dark/20 px-3 py-1 shadow-md backdrop-blur-sm transition md:-translate-y-full md:group-hover:translate-y-0"
        >
          <div
            class="flex flex-auto items-center justify-evenly gap-2 text-light"
          >
            <Torcher :stream="stream" :capabilities="capabilities" />
            <TButton
              :icon="grid ? 'grid_on' : 'grid_off'"
              class="noTapHighlight rounded-full p-2"
              :class="[grid && 'text-warning']"
              @click="grid = !grid"
            />
          </div>
        </div>
        <div class="absolute inset-x-0 bottom-0 grid grid-cols-3 py-1">
          <div></div>
          <div class="flex items-center justify-center">
            <Capture :stream="stream" @capture="onCapture" />
          </div>
          <div class="flex items-center text-light">
            <TButton
              v-if="devices.length > 1"
              icon="cameraswitch"
              class="noTapHighlight rounded-full p-2"
              @click="cycle"
            />
          </div>
        </div>
      </div>
      <div
        v-else
        class="text-center font-semibold text-primary"
        :class="[_wrapperClass]"
      >
        Loading Devices...
      </div>
    </template>
  </MediaDevices>
</template>

<script setup>
import { useVModel } from "@vueuse/core";
import { computed, defineAsyncComponent, ref, watch } from "vue";

const VideoStream = defineAsyncComponent(() => import("./stream.vue"));
const MediaDevices = defineAsyncComponent(() => import("./devices.vue"));
const GridOverlay = defineAsyncComponent(() => import("./overlays/grid.vue"));
const Torcher = defineAsyncComponent(() => import("./torcher.vue"));
const Capture = defineAsyncComponent(() => import("./capture.vue"));

const props = defineProps({
  modelValue: Object,
  wrapperClass: {
    type: [String, Object],
    default: "",
  },
});
const emit = defineEmits(["update:modelValue", "capture"]);

const grid = ref(true);
const imgFile = ref(props.modelValue ?? null);

const data = useVModel(props, "modelValue", emit);
const imgSrc = computed(() =>
  !!imgFile.value ? URL.createObjectURL(imgFile.value) : null
);
const _wrapperClass = computed(() =>
  Array.isArray(props.wrapperClass) ? props.wrapperClass : [props.wrapperClass]
);

const onCapture = (img) => {
  imgFile.value = img;
};

watch(imgFile, (img) => {
  emit("capture", img);
  data.value = img;
});
</script>
