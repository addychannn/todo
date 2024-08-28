<template>
  <div
    class="bg-dots bg-dots-animated h-screen absolute inset-0 flex flex-col items-center justify-center gap-5"
  >
    <TImage
      src="/favicons/baguioseal-animated.svg"
      class="aspect-square h-16 w-16 rounded-full bg-foreground sm:h-32 sm:w-32"
    />
    <div class="text-4xl md:text-7xl">{{ $system.product_name }}</div>
    <div>
      <div
        class="grid max-w-[95dvw] gap-2 rounded-xl border border-foreground/25 bg-foreground/10 p-2 backdrop-blur-[2px]"
      >
        <div class="flex flex-wrap items-center justify-center gap-1">
          <div class="flex flex-col items-center justify-center">
            <TImage
              v-if="!!authStore.profile?.image"
              :src="authStore.profile?.image?.thumbnails.medium"
              class="aspect-square w-24 rounded-2xl border-2 border-light"
            />
            <div
              v-else
              class="box-border flex aspect-square w-24 items-center justify-center rounded-2xl border-2 border-light bg-dark/75 leading-none text-light"
            >
              <TIcon name="person" size="3xl" class="" />
            </div>
            <div class="flex items-center text-sm">
              <div class="flex-auto">
                {{ authStore.profile?.full_name ?? authStore.username }}
              </div>
            </div>
            <div class="mb-2 text-xs">{{ authStore.email }}</div>
            <TButton
              icon="logout"
              iconSize="sm"
              label="Logout"
              class="rounded-full px-3 py-1"
              @click="authStore.logout()"
            />
          </div>
          <TPin
            v-model="pin"
            @update:modelValue="unlock"
            :error="error"
            :errorMessage="errorMessage"
            confirmation
          />
        </div>
      </div>
      <div class="text-xs font-semibold">
        <span class="">Powered By: </span> Management Information Technology
        Division
      </div>
    </div>
    <div class="fixed right-7 top-7 flex items-center justify-center">
      <ThemeToggle class="rounded-full p-1" />
    </div>
  </div>
</template>

<script setup>
import { inject, ref } from "vue";
import { useAuthStore } from "@/stores";

const $system = inject("$system");

const pin = ref();
const error = ref(false);
const errorMessage = ref("");
const authStore = useAuthStore();
const timer = ref(null);

const unlock = () => {
  error.value = false;
  if (!!pin.value && authStore.verifyPin(pin.value)) {
    authStore.unlock(pin.value);
  } else {
    error.value = true;
    errorMessage.value = "Invalid PIN!";
    if (!!timer.value) {
      clearTimeout(timer.value);
    }
    timer.value = setTimeout(() => {
      error.value = false;
    }, 3000);
  }
};
</script>
