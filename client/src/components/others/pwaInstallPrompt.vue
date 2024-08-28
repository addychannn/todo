div
<template>
  <teleport
    v-if="!!pwaPrompt && !hidden && !systemStore.settings.pwa?.doNotShow"
    to="body"
  >
    <UseDraggable ref="draggable" class="!fixed" :initialValue="initialPos">
      <div
        class="pointer-events-none flex w-[95dvw] max-w-xs flex-col overflow-hidden rounded-2xl border border-primary bg-light text-dark shadow-lg shadow-primary/25"
      >
        <div class="flex items-center bg-primary py-1 pl-3 pr-1 text-light">
          <div class="flex-auto font-semibold">
            {{ $system.product_name }}
          </div>
          <TButton
            icon="close"
            iconSize="sm"
            class="pointer-events-auto rounded-full p-1"
            @click="close"
          />
        </div>
        <div class="flex-auto px-3 py-1">
          This app can be installed as an application.
        </div>
        <div class="flex items-center gap-1 px-3 py-2">
          <TButton
            label="Install"
            class="pointer-events-auto rounded bg-primary px-3 py-1 text-light"
            @click="installPWA"
          />
          <div
            class="flex items-center gap-0.5 rounded border border-primary text-gray-600 first:[&>button]:rounded-l last:[&>button]:rounded-r [&>div]:border-l [&>div]:border-primary"
          >
            <TButton
              label="Not Now"
              class="pointer-events-auto px-3 py-1"
              @click="close"
            />
            <TPopover
              icon="arrow_drop_down"
              class="[&>button]:pointer-events-auto"
            >
              <div class="flex bg-light text-dark">
                <TButton label="Never" class="px-4 py-2" @click="never" />
              </div>
            </TPopover>
          </div>
        </div>
      </div>
    </UseDraggable>
  </teleport>
</template>

<script setup>
import { computed, inject, onMounted, ref } from "vue";
import { useSystemStore } from "@/stores";
import { UseDraggable } from "@vueuse/components";

const pwaPrompt = inject("pwaInstallPrompt");
const systemStore = useSystemStore();

const hidden = ref(false);
const permaHide = ref(false);
const draggable = ref(null);
const initialPos = ref({
  x: 256,
  y: 64,
});

const installPWA = () => {
  pwaPrompt.value.prompt();
  pwaPrompt.value.userChoice
    .then((result) => {
      if (result.outcome == "accepted") {
      }
    })
    .finally(() => {
      close();
    });
};

const close = () => {
  hidden.value = true;
  clearPrompt();
  if (permaHide.value) {
    systemStore.settings.pwa.doNotShow = true;
  }
};

const never = () => {
  permaHide.value = true;
  close();
};

const clearPrompt = () => {
  pwaPrompt.value = null;
};

onMounted(() => {
  if (systemStore.settings.pwa.doNotShow) {
    clearPrompt();
  } else {
    let body = document.getElementsByTagName("body")[0];
    initialPos.value.x =
      body.offsetWidth - (draggable.value?.$el.offsetWidth ?? 320) - 30;
  }
});
</script>
