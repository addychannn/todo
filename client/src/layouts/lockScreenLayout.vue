<template>
  <KeepAlive exclude="lockScreen">
    <Layout v-if="!authStore.locked" v-bind="$attrs">
      <slot />
    </Layout>
    <LockScreen v-else />
  </KeepAlive>
</template>

<script setup>
import { ref } from "vue";
import { useAuthStore } from "@/stores";
import LockScreen from "./main/lockScreen.vue";

const authStore = useAuthStore();
</script>
