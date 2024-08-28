<template>
  <TButton
    :icon="torch ? 'flash_on' : 'flash_off'"
    class="noTapHighlight rounded-full p-2"
    :class="[torch && 'text-warning']"
    :disabled="!canTorch"
    @click="torch = !torch"
  />
</template>

<script setup>
import { computed, onMounted, ref, watch } from "vue";
const props = defineProps({
  stream: Object,
  capabilities: Object,
});
const emit = defineEmits(["toggle"]);

const torch = ref(false);
const torching = ref(false);

const canTorch = computed(() => !!props.capabilities?.torch);

const setTorch = (on = false) => {
  let [track] = props.stream.getVideoTracks();
  track.applyConstraints({
    advanced: [{ torch: on }],
  });
};

watch(torch, (val) => {
  setTorch(val);
});

onMounted(() => {});
</script>
