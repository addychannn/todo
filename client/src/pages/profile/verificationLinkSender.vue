<template>
  <TButton
    class="rounded-full border border-foreground/25 bg-glossy px-3 py-1"
    :disabled="loading"
    @click="sendVerificationLink"
  >
    <div class="pointer-events-none flex items-center gap-2">
      <TIcon
        :name="loading ? 'motion_photos_on' : done ? 'done' : 'send'"
        size="sm"
        :class="{ 'animate-spin': loading }"
      />
      <div class="flex-auto font-semibold">Send Email Verification Link</div>
    </div>
  </TButton>
</template>

<script setup>
import { inject, ref } from "vue";
import { notify } from "@/scripts";

const $api = inject("$api");

const loading = ref(false);
const done = ref(false);

const sendVerificationLink = () => {
  loading.value = true;

  $api
    .post("/email/resend")
    .then((response) => {
      done.value = true;

      notify({
        title: "Success!",
        type: "positive",
        text: response.data.message,
      });

      setTimeout(() => {
        done.value = false;
      }, 3000);
    })
    .finally(() => {
      loading.value = false;
    });
};
</script>
