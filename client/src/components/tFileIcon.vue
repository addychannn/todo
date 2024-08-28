<template>
  <component :is="icon" v-bind="$attrs" class="inline-block"></component>
</template>

<script setup>
import { computed, defineAsyncComponent, onMounted, ref } from "vue";
import catalog from "./fileIcons/catalog.json";

const props = defineProps({
  name: String,
});

const iconName = computed(() =>
  catalog.find((ico) => ico == props.name.toLowerCase())
    ? props.name.toLowerCase()
    : "blank"
);

const icon = computed(() =>
  defineAsyncComponent(() => import(`./fileIcons/${iconName.value}.vue`))
);
</script>
