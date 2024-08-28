<template>
  <TButton
    class="noTapHighlight aspect-square w-16 rounded-full p-1"
    :disabled="loading"
    @click="captureImage"
  >
    <div
      class="pointer-events-none flex aspect-square items-center justify-center rounded-full border-4 border-light leading-none outline outline-1 outline-dark/50"
    >
      <TIcon
        v-if="loading"
        name="motion_photos_on"
        size="xl"
        class="animate-spin text-light"
      />
    </div>
  </TButton>
</template>

<script setup>
import { computed, ref, watch } from "vue";
import { MimeTypes } from "@/scripts";
const props = defineProps({
  stream: Object,
  prefix: {
    type: String,
    default: "IMG",
  },
});

const emit = defineEmits(["loading", "capture"]);

const loading = ref(false);

const track = computed(() => {
  let [tracks] = props.stream.getVideoTracks();
  return tracks;
});
const settings = computed(() => track.value.getSettings());
const capabilities = computed(() => track.value.getCapabilities());

const captureImage = async () => {
  loading.value = true;
  const imageCapture = new ImageCapture(track.value);
  imageCapture
    .grabFrame()
    .then(async (bmp) => {
      let blob = await drawCanvasImage(bmp);
      let imageFile = new File([blob], createFileName(blob.type), {
        type: blob.type,
      });
      emit("capture", imageFile);
    })
    .finally(() => {
      loading.value = false;
    });
};

const createFileName = (mime) => {
  return `${createName()}.${getExtension(mime)}`;
};

const createName = () => {
  let _date = new Date();
  let _month = `${_date.getMonth()}`.padStart(2, "0");
  let _day = `${_date.getDate()}`.padStart(2, "0");
  let _dateStamp = `${_date.getFullYear()}${_month}${_day}`;
  let _hours = `${_date.getHours()}`.padStart(2, "0");
  let _minutes = `${_date.getMinutes()}`.padStart(2, "0");
  let _seconds = `${_date.getSeconds()}`.padStart(2, "0");
  let _timeStamp = `${_hours}${_minutes}${_seconds}`;
  return `${props.prefix}_${_dateStamp}_${_timeStamp}`;
};

const getExtension = (mime) => {
  return MimeTypes.extension(mime);
};

const drawCanvasImage = async (bitmapImage) => {
  const canvas = document.createElement("canvas");
  canvas.width = bitmapImage.width;
  canvas.height = bitmapImage.height;
  const ctx = canvas.getContext("bitmaprenderer");
  ctx.transferFromImageBitmap(bitmapImage);
  return await new Promise((res) => canvas.toBlob(res));
};

watch(loading, (val) => {
  emit("loading");
});
</script>
