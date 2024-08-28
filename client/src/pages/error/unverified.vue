<template>
  <div>
    <Error
      :code="401"
      error="Unauthorized!"
      description="You need to verify your email address to continue!"
      :links="links"
    >
      <template #links>
        <NavLinks :links="links">
          <template #default="{ link, index, total }">
            <VerificationLinkSender v-if="link.type == 'verificationSender'" />
            <TButton
              v-else
              :icon="link.icon"
              iconSize="sm"
              :label="link.label"
              :to="link.to ?? null"
              @click="link.action ? link.action() : null"
              v-bind="link.bindings ?? {}"
              class="rounded-md px-3 py-1"
            />
            <div
              v-if="index != total - 1"
              class="border-l border-slate-700/25"
            />
          </template>
        </NavLinks>
      </template>
    </Error>
  </div>
</template>

<script setup>
import {
  defineAsyncComponent,
  inject,
  onBeforeUnmount,
  onMounted,
  ref,
} from "vue";
import { useRoute, useRouter } from "vue-router";
import { useAuthStore, useSystemStore } from "@/stores";
import Error from "./error.vue";
import NavLinks from "./links.vue";

const VerificationLinkSender = defineAsyncComponent(() =>
  import("../profile/verificationLinkSender.vue")
);

const $api = inject("$api");
const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();
const systemStore = useSystemStore();

const checker = ref({
  timer: null,
});

const links = ref([
  {
    label: "Resend Verification Link",
    icon: "send",
    type: "verificationSender",
    bindings: {
      class: "bg-glossy border border-foreground/25",
    },
  },
]);

const verifiedChecker = () => {
  systemStore.setLoading("Checking if email is verified...");
  $api
    .get("/email/isVerified")
    .then((response) => {
      if (!!response.data.verified) {
        authStore.verified = response.data.verified;
        redirect();
      } else {
        cleanUp();
        checker.value.timer = setTimeout(() => verifiedChecker(), 5000);
      }
    })
    .finally(() => {
      systemStore.setLoading(null);
    });
};

const cleanUp = () => {
  clearTimeout(checker.value.timer);
};

const redirect = () => {
  router.push(route.query.redirect || { name: "profile" });
};

onMounted(() => {
  if (!!authStore.verified) {
    redirect();
  } else {
    verifiedChecker();
  }
});

onBeforeUnmount(() => {
  cleanUp();
});
</script>
