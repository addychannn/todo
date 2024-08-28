<template>
  <video class="h-full w-full object-cover" ref="playback" autoplay></video>
</template>

<script setup>
import { onMounted, ref } from "vue";
const props = defineProps({
  stream: Object,
});

const playback = ref(null);

const startPlayBack = (stream) => {
  if (!!stream) {
    playback.value.srcObject = stream;
  }
};

onMounted(() => {
  startPlayBack(props.stream);
});
</script>
