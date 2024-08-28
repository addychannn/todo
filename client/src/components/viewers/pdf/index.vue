<template>
  <div class="relative">
    <VuePdfApp v-if="!!pdf" :pdf="pdf" :theme="theme" @open="openHandler">
      <template v-for="(index, name) in slots" v-slot:[name]="data">
        <slot :name="name" v-bind="data"></slot>
      </template>
      <template #toolbar-right-append>
        <TButton
          label="Close"
          class="toolbarButton !w-auto"
          @click="emit('close')"
        />
      </template>
    </VuePdfApp>
  </div>
</template>

<script setup>
import "vue3-pdf-app/dist/icons/main.css";
import { computed, onMounted, ref, useSlots } from "vue";
import VuePdfApp from "vue3-pdf-app";
import { Helpers, jvalidate } from "@/scripts";
import { useSystemStore } from "@/stores";
const props = defineProps({
  src: String,
});
const emit = defineEmits(["close"]);

const slots = useSlots();

const { Validators } = new jvalidate();
const sytemStore = useSystemStore();

const pdf = ref(null);

const theme = computed(() => (sytemStore.theme.dark ? "dark" : "light"));

const fetchPDF = async (url) => {
  let _url = url;
  if (!Validators.url.method(url)) {
    _url = constructURL(url);
  }

  let response = await fetch(_url, { credentials: "include" });
  let data = await response.arrayBuffer();
  pdf.value = URL.createObjectURL(
    new Blob([data], {
      type: "application/pdf",
    })
  );
};

const constructURL = (route) => {
  return `${Helpers.stripSlashes(
    import.meta.env.VITE_SERVER
  )}/${Helpers.stripSlashes(route)}`;
};

const openHandler = (pdfApp) => {
  pdfApp.preferences.prefs.printResolution = 300; // Set resolution
  pdfApp._readPreferences(); // Update cached preferences
};

onMounted(() => {
  fetchPDF(props.src);
});
</script>

<style scoped lang="scss">
:deep(#thumbnailView) {
  @apply w-full #{!important};
}
:deep(#viewerContainer) {
  @apply p-2;

  #viewer {
    @apply flex flex-col items-center gap-3 #{!important};
    &.scrollHorizontal {
      @apply flex-row  #{!important};
    }

    div {
      border-image: none !important;
      @apply border-0 border-none shadow-md;
    }
  }
}
</style>
