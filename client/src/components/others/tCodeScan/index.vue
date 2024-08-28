<template>
  <div
    :class="[
      isFullscreen &&
        canControl('fullscreen') &&
        'fixed inset-0 z-[99999] max-h-[100dvh] max-w-[100dvw] ',
      !isFullscreen && 'rounded-lg',
    ]"
    class="bg-background"
  >
    <QrcodeStream
      v-if="!destroyed && device !== null"
      @detect="onDetect"
      @camera-on="onReady"
      @error="onError"
      :formats="accept"
      :paused="_paused"
      camera="off"
      :constraints="{ deviceId: device.deviceId }"
      :track="trackDetected"
      class="overflow-hidden"
      :class="[(!isFullscreen || canControl('fullscreen')) && 'rounded-lg']"
    >
      <slot name="prepend" v-bind="slotBindings"></slot>
      <slot v-bind="slotBindings">
        <div
          v-if="!loading && !destroyed && !paused"
          class="group relative flex h-full w-full flex-col items-center justify-center"
        >
          <div
            v-if="canControlAny(['torch', 'device-cycle'])"
            class="absolute inset-x-0 top-0 flex -translate-y-full items-center justify-evenly gap-2 bg-dark/50 px-3 py-1 text-light shadow-md transition group-hover:translate-y-0"
          >
            <slot name="header">
              <TButton
                v-if="canControlAny('torch')"
                :icon="torch ? 'flash_on' : 'flash_off'"
                class="rounded-full p-2"
                :disabled="!canTorch"
                @click="toggleTorch()"
              />
              <TButton
                v-if="canControlAny('device-cycle')"
                icon="cameraswitch"
                class="rounded-full p-2"
                :disabled="devices.length <= 1"
                @click="cycleDevices"
              />
            </slot>
          </div>
          <ScanOverlay v-if="scanOverlay" :isPortrait="isPortrait" />
          <div
            v-if="
              (canZoom && canControlAny('zoom')) || canControlAny('fullscreen')
            "
            class="absolute inset-x-0 bottom-0 flex items-center justify-center gap-1 bg-dark/50 px-3 py-1 text-light shadow-md backdrop-blur-sm"
          >
            <div
              v-if="canZoom && canControlAny('zoom')"
              class="flex flex-auto items-center gap-2 px-2"
            >
              <TIcon name="zoom_out" size="sm" />
              <input
                type="range"
                :min="zoom.min"
                :max="zoom.max"
                :step="zoom.step"
                :value="zoom.current"
                class="bg-gray-20 my-6 h-1 flex-auto cursor-pointer appearance-none rounded-lg"
                @input="onZoom"
              />
              <TIcon name="zoom_in" size="sm" />
            </div>
            <TButton
              v-if="canControlAny('fullscreen')"
              :icon="isFullscreen ? 'fullscreen_exit' : 'fullscreen'"
              class="rounded-full p-2"
              @click="toggleFS"
            />
          </div>
        </div>
        <slot name="loading" v-bind="slotBindings">
          <div v-if="loading" class="text-xl italic text-gray-400">
            Loading device....
          </div>
        </slot>
      </slot>
      <slot name="append" v-bind="slotBindings"></slot>
    </QrcodeStream>
    <SizeObserver @resize="onResize" />
  </div>
</template>

<script setup>
import {
  computed,
  defineAsyncComponent,
  nextTick,
  onBeforeUnmount,
  onMounted,
  ref,
  watch,
} from "vue";
import { QrcodeStream } from "vue-qrcode-reader";
import { useFullscreen, useVModel } from "@vueuse/core";

const ScanOverlay = defineAsyncComponent(() => import("./overlay.vue"));

const { isFullscreen, toggle: toggleFS } = useFullscreen();

const props = defineProps({
  paused: {
    type: Boolean,
    default: false,
  },

  accept: {
    type: Object,
    default: ["code_128"],
    validator: (val) => {
      let accepted = [
        "aztec",
        "code_128",
        "code_39",
        "code_93",
        "codabar",
        "data_matrix",
        "ean_13",
        "ean_8",
        "itf",
        "pdf417",
        "qr_code",
        "upc_a",
        "upc_e",
        "unknown",
      ];

      let valid = val.filter((item) => accepted.indexOf(item) > -1);

      return valid.length > 0;
    },
  },

  scanOverlay: {
    type: Boolean,
    default: false,
  },

  boundingBox: {
    type: Boolean,
    default: false,
  },
  outline: {
    type: Boolean,
    default: false,
  },

  controls: {
    type: Object,
    default: () => ["zoom", "torch", "device-cycle", "fullscreen"],
  },
});
const emit = defineEmits(["detect", "update:paused", "error"]);

const devices = ref([]);
const device = ref(null);
const loading = ref(true);
const torch = ref(false);
const canTorch = ref(false);
const destroyed = ref(false);
const zoom = ref({
  min: 1,
  max: 10,
  step: 0.1,
  current: 1,
});
const canZoom = ref(false);
const currentDevice = ref(null);
const isPortrait = ref(false);

const _paused = useVModel(props, "paused", emit);

const slotBindings = computed(() => ({
  loading: loading.value,
  destroyed: destroyed.value,
  paused: _paused.value,
  isPortrait: isPortrait.value,
  devices: devices.value,

  canTorch: canTorch.value,
  torched: torch.value,
  torch: async (on) => torcher(on),

  canZoom: canZoom.value,
  zoom: zoom.value,
  onZoom: (e) => onZoom(e),

  reload: () => reload(),
  cycleDevices: () => cycleDevices(),
  setDevice: (dvc) => setDevice(dvc),
}));

const onDetect = async (e) => {
  _paused.value = true;
  emit("detect", e);
};

const onReady = async (e) => {
  stopTracks(currentDevice.value);
  currentDevice.value = await navigator.mediaDevices.getUserMedia({
    audio: false,
    video: {
      deviceId: device.value.deviceId,
    },
  });

  loading.value = false;
  canTorch.value = !!e.torch;
  canZoom.value = !!e.zoom;
  torch.value = false;
  zoomable(e);
};

const onError = (e) => {
  emit("error", e);
};

const reload = async () => {
  loading.value = true;
  destroyed.value = true;
  await nextTick();
  destroyed.value = false;
};

const getDevices = async () => {
  devices.value = (await navigator.mediaDevices.enumerateDevices()).filter(
    ({ kind }) => kind === "videoinput"
  );
  let useIndex = 0;

  if (devices.value.length > 0) {
    for (let $i = 0; $i < devices.value.length; $i++) {
      let capable = devices.value[$i].getCapabilities();
      if (capable.facingMode.indexOf("environment") > -1) {
        useIndex = $i;
      }
    }
    setDevice(devices.value[useIndex]);
  }
};

const cycleDevices = () => {
  let index = devices.value.indexOf(device.value);
  let tmp = devices.value[index + 1];
  torcher(false);
  setDevice(tmp ?? devices.value[0]);
  reload();
};

const setDevice = (dvc) => {
  device.value = dvc;
};

const toggleTorch = async () => {
  if (!!canTorch.value) {
    torch.value = !torch.value;
    await torcher(torch.value);
  }
};

const torcher = async (on = false) => {
  if (!!canTorch.value) {
    let [track] = currentDevice.value.getVideoTracks();
    track.applyConstraints({
      advanced: [{ torch: on }],
    });
  }
};

const zoomable = (capabilities) => {
  if (!!capabilities.zoom) {
    let [track] = currentDevice.value.getVideoTracks();
    let settings = track.getSettings();
    zoom.value.min = capabilities.zoom.min;
    zoom.value.max = capabilities.zoom.max;
    zoom.value.step = capabilities.zoom.step;
    zoom.value.current = settings.zoom;
  }
};

const onZoom = (val) => {
  let [track] = currentDevice.value.getVideoTracks();
  track.applyConstraints({ advanced: [{ zoom: val.target.value }] });
};

const trackDetected = (detectedCodes, ctx) => {
  if (props.boundingBox) {
    paintBoundingBox(detectedCodes, ctx);
  }
  if (props.outline) {
    paintOutline(detectedCodes, ctx);
  }
};

const paintBoundingBox = (detectedCodes, ctx) => {
  for (const detectedCode of detectedCodes) {
    const {
      boundingBox: { x, y, width, height },
    } = detectedCode;

    ctx.lineWidth = 2;
    ctx.strokeStyle = "#007bff";
    ctx.strokeRect(x, y, width, height);
  }
};

const paintOutline = (detectedCodes, ctx) => {
  for (const detectedCode of detectedCodes) {
    const [firstPoint, ...otherPoints] = detectedCode.cornerPoints;

    ctx.strokeStyle = "red";

    ctx.beginPath();
    ctx.moveTo(firstPoint.x, firstPoint.y);

    for (const { x, y } of otherPoints) {
      ctx.lineTo(x, y);
    }
    ctx.lineTo(firstPoint.x, firstPoint.y);
    ctx.closePath();
    ctx.stroke();
  }
};

const onResize = (size) => {
  isPortrait.value = size.width < size.height;
};

const stopTracks = async (mediaDevice) => {
  if (!!mediaDevice) {
    let tracks = mediaDevice.getTracks();

    for (const track of tracks) {
      mediaDevice.removeTrack(track);
      await track.stop();
    }
  }
};

const cleanUp = async () => {
  // Temporary disabled - Won't clear other instances of usermedia
  // for (const devce of devices.value) {
  //   let tmpDevice = await navigator.mediaDevices.getUserMedia({
  //     audio: false,
  //     video: {
  //       deviceId: devce.deviceId,
  //     },
  //   });
  //   stopTracks(tmpDevice);
  // }
  stopTracks(currentDevice.value);
};

const canControlAny = (options) => {
  if (!!Array.isArray(options)) {
    return options.some((item) => canControl(item));
  } else {
    return canControl(options);
  }
};

const canControl = (option) => {
  return props.controls.includes(option);
};

watch(isFullscreen, (val) => {
  reload();
});

onMounted(async () => {
  if (canControl("fullscreen")) {
    window.addEventListener("keydown", (e) => {
      // f11
      if (e.code === "F11") {
        e.preventDefault();
        toggleFS();
      }
    });
  }
  await getDevices();
});

onBeforeUnmount(() => {
  cleanUp();
});
</script>
