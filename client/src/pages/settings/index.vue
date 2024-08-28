<template>
  <Page class="p-3" :class="[transitioning && 'overflow-hidden']">
    <router-view v-slot="{ Component }">
      <transition
        enter-from-class="opacity-0 blur-md"
        leave-to-class="opacity-0 blur-md"
        enter-active-class="transition duration-300 delay-300"
        leave-active-class="transition duration-300"
        @before-leave="transitioning = true"
        @after-enter="transitioning = false"
      >
        <component
          :is="Component"
          class="border border-foreground/10 bg-background/10 backdrop-blur-sm"
        >
        </component>
      </transition>
    </router-view>
  </Page>
</template>

<script setup>
import { ref } from "vue";

const transitioning = ref(false);
</script>
