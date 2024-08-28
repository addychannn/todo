<template>
  <LSLayout
    class="bg-dots !relative"
    :style="{
      paddingTop: systemStore.settings.navbar.fixed
        ? `${headerSize.height}px`
        : '',
    }"
    ref="publicLayout"
  >
    <TopNav :layoutRef="publicLayout" @resize="(size) => (headerSize = size)" />
    <PageContainer>
      <router-view v-slot="{ Component }">
        <transition
          enter-from-class="opacity-0"
          leave-to-class="opacity-0"
          enter-active-class="transition duration-300 delay-300"
          leave-active-class="transition duration-300"
        >
          <component :is="Component"> </component>
        </transition>
      </router-view>
    </PageContainer>
    <Footer class="flex border-t border-foreground/25 bg-page-background p-0">
      <ExpandedFooter />
    </Footer>
    <TScrollUp
      class="bottom-14 right-8 z-50 rounded-2xl bg-gray-900/75 px-5 py-2 text-gray-100 shadow-md dark:bg-gray-500/75"
    >
      <TIcon name="keyboard_double_arrow_up" size="sm" />
    </TScrollUp>
  </LSLayout>
</template>

<script setup>
import { ref } from "vue";
import { useSystemStore } from "@/stores";
import LSLayout from "./lockScreenLayout.vue";
import ExpandedFooter from "./footer/expanded.vue";
import TopNav from "./topNav.vue";

const systemStore = useSystemStore();

const publicLayout = ref(null);
const headerSize = ref({
  width: 0,
  height: 0,
});
</script>
