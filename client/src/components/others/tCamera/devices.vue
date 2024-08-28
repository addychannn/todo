<template>
  <Wrapper>
    <slot v-bind="_bindings"></slot>
  </Wrapper>
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

const Wrapper = defineAsyncComponent(() => import("../../wrapper.vue"));

const props = defineProps({
  pause: {
    type: Boolean,
    default: false,
  },
  constraints: {
    type: Object,
    default: () => ({}),
  },
});
const emit = defineEmits(["error"]);

const error = ref(null);
const loading = ref(false);
const devices = ref([]);
const capabilities = ref({});
const currentDevice = ref(null);
const currentStream = ref(null);
const prompting = ref(false);

const _bindings = computed(() => ({
  error: error.value,
  loading: loading.value,
  devices: devices.value,
  device: currentDevice.value,
  stream: currentStream.value,
  prompting: prompting.value,
  reload: reload,
  cycle: cycleDevices,
  changeDevice: selectDevice,
  capabilities: capabilities.value,
}));

const checkPermission = async () => {
  try {
    if (
      "mediaDevices" in navigator &&
      "getUserMedia" in navigator.mediaDevices
    ) {
      let result = await navigator.permissions.query({ name: "camera" });
      if (result.state == "prompt") {
        prompting.value = true;
      }
      if (result.state != "granted") {
        let _devices = await navigator.mediaDevices.getUserMedia({
          audio: false,
          video: true,
        });
        await stopTracks(_devices);
      }
      return true;
    }

    throw {
      code: "NotSupportedError",
      message: "Access to media devices are not suppported!",
    };
  } catch (e) {
    error.value = e;
    return false;
  } finally {
    prompting.value = false;
  }
};

const getDevices = async () => {
  return (await navigator.mediaDevices.enumerateDevices()).filter(
    ({ kind }) => kind == "videoinput"
  );
};

const selectInitialDevice = () => {
  let useIndex = 0;

  if (devices.value.length > 0) {
    for (let $i = 0; $i < devices.value.length; $i++) {
      let capable = devices.value[$i].getCapabilities();
      if (capable.facingMode.indexOf("environment") > -1) {
        useIndex = $i;
      }
    }
    selectDevice(devices.value[useIndex]);
  }
};

const cycleDevices = () => {
  let index = devices.value.indexOf(currentDevice.value);
  let tmp = devices.value[index + 1];
  selectDevice(tmp ?? devices.value[0]);
};

const selectDevice = async (_device) => {
  try {
    loading.value = true;
    currentDevice.value = _device;
    await changeStream(_device);
  } catch (e) {
    error.value = e;
  } finally {
    loading.value = false;
  }
};

const changeStream = async (_device) => {
  await stopTracks(currentStream.value);
  currentStream.value = null;
  nextTick();
  currentStream.value = await navigator.mediaDevices.getUserMedia({
    audio: false,
    video: {
      deviceId: _device.deviceId,
    },
  });
  let [track] = currentStream.value.getTracks();
  capabilities.value = track.getCapabilities();
  await track.applyConstraints({
    advanced: [
      {
        width: 1366,
        height: 768,
      },
    ],
  });
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
  for (const devce of devices.value) {
    let tmpDevice = await navigator.mediaDevices.getUserMedia({
      audio: false,
      video: {
        deviceId: currentDevice.value.deviceId,
      },
    });
    stopTracks(tmpDevice);
  }
  await stopTracks(currentStream.value);
  devices.value = [];
  currentStream.value = currentDevice.value = null;
};

const reload = async () => {
  await cleanUp();
  devices.value = await getDevices();
  selectDevice(devices.value[0]);
};

watch(
  () => props.pause,
  (val) => {
    if (val) {
      stopTracks(currentStream.value);
    } else {
      selectDevice(currentDevice.value);
    }
  }
);

watch(error, (e) => {
  emit("error", {
    code: "NotAllowedError",
    message: e.message,
  });
});

onMounted(async () => {
  if (await checkPermission()) {
    devices.value = await getDevices();
    selectInitialDevice();
  }
});

onBeforeUnmount(async () => {
  await cleanUp();
});
</script>
