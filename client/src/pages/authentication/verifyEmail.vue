<template>
  <div class="flex flex-col items-center justify-center gap-2">
    <TCard class="w-[95dvw] max-w-xs bg-opacity-5 backdrop-blur-sm">
      <TCardBody class="flex flex-col items-center justify-center gap-1">
        <div class="flex items-center justify-center py-3">
          <TIcon
            v-if="loading"
            name="motion_photos_on"
            size="2xl"
            class="animate-spin"
          />
          <span
            v-if="!loading && !done && !error"
            class="relative flex items-center justify-center"
          >
            <TIcon
              name="email"
              type="filled"
              size="2xl"
              class="z-20 text-primary"
            />
            <span
              class="absolute left-1/2 top-1/2 z-10 -translate-x-1/2 -translate-y-1/2 bg-light px-5 py-4"
            ></span>
          </span>
          <span
            v-if="!loading && done && !error"
            class="relative flex items-center justify-center"
          >
            <TIcon
              name="check_circle"
              type="filled"
              size="2xl"
              class="z-20 text-positive"
            />
            <span
              class="absolute left-1/2 top-1/2 z-10 -translate-x-1/2 -translate-y-1/2 bg-light px-[1.125rem] py-4"
            ></span>
          </span>
          <span
            v-if="!loading && done && error"
            class="relative flex items-center justify-center"
          >
            <TIcon
              name="error"
              type="filled"
              size="2xl"
              class="z-20 text-negative"
            />
            <span
              class="absolute left-1/2 top-1/2 z-10 -translate-x-1/2 -translate-y-1/2 bg-light px-[1.125rem] py-4"
            ></span>
          </span>
        </div>
        <div
          class="flex items-center justify-center gap-1 self-stretch text-center"
        >
          {{ message }}
        </div>
      </TCardBody>
    </TCard>
    <NavLinks />
  </div>
</template>
<script setup>
import {
  computed,
  defineAsyncComponent,
  inject,
  onBeforeUnmount,
  onMounted,
  ref,
} from "vue";
import { useRoute, useRouter } from "vue-router";
import { useAuthStore } from "@/stores";

const NavLinks = defineAsyncComponent(() => import("@/pages/error/links.vue"));

const $api = inject("$api");
const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();

const loading = ref(false);
const error = ref(false);
const done = ref(false);
const message = ref("");
const countDown = ref(null);

const redirectCtr = () => {
  let duration = 5;
  countDown.value = setInterval(() => {
    message.value = `Redirecting in ${duration} seconds...`;
    if (duration <= 0) {
      clearInterval(countDown.value);
      redirect();
    }
    duration--;
  }, 1000);
};

const redirect = () => {
  router.push(route.query.redirect || { name: "profile" });
};

const verifyEmail = () => {
  loading.value = true;
  message.value = "Verifying account, pleas wait...";

  let url = `/email/verify/${route.params.id}`;
  $api
    .get(url, {
      params: route.query,
    })
    .then((response) => {
      message.value = "Verification process complete!";
      if (!!response.data.verified) {
        error.value = false;
        authStore.verified = response.data.verified;
        redirectCtr();
      }
    })
    .catch((e) => {
      message.value = "Failed to verify account! Please try again.";
      error.value = true;
    })
    .finally(() => {
      done.value = true;
      loading.value = false;
    });
};

onMounted(() => {
  verifyEmail();
});

onBeforeUnmount(() => {
  clearInterval(countDown.value);
});
</script>
