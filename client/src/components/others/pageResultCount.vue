<template>
  <div
    class="flex items-center gap-1 text-sm font-semibold italic text-gray-400"
  >
    <div class="flex-auto leading-none">About {{ total }} results</div>
    <div class="leading-none">
      {{ pageString }}
    </div>
  </div>
</template>
<script setup>
import { computed, ref } from "vue";
const props = defineProps({
  count: Number,
  total: Number,
  limit: Number,
  offset: Number,
});

const pageString = computed(() => {
  var startRecord = props.offset + 1;
  var endRecord = props.offset + props.count;
  endRecord = endRecord > props.total ? props.total : endRecord;

  var pageString =
    "Showing " + startRecord + " - " + endRecord + " of " + props.total;
  return pageString;
});
</script>
