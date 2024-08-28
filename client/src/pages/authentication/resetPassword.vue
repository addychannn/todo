<template>
  <TCard
    tag="form"
    class="relative w-[95dvw] max-w-[300px] rounded-2xl border border-slate-500/50 bg-slate-100 p-2 text-slate-900 shadow-md"
    @submit.prevent="login"
  >
    <TCardBody class="h-12"> </TCardBody>
    <TCardBody class="">
      <div class="pb-3 text-center text-xl">
        <span class="font-bold">Create</span> a new password!
      </div>
      <div class="pb-3 text-center">
        Enter and verify a new, secure password for your account.
      </div>
      <TInput
        :label="form.email.name"
        v-model="form.email.value"
        :error="form.email.error"
        :errorMessage="form.email.errorMessage"
        innerClass="text-dark bg-light"
      >
        <template #prepend="{ error }">
          <TIcon
            name="email"
            class="select-none"
            :class="{ 'text-negative': error, 'text-gray-500': !error }"
          />
        </template>
      </TInput>
      <TInput
        :label="form.password.name"
        v-model="form.password.value"
        :error="form.password.error"
        :errorMessage="form.password.errorMessage"
        type="password"
        innerClass="text-dark bg-light"
      >
        <template #prepend="{ error }">
          <TIcon
            name="lock"
            class="select-none"
            :class="{ 'text-negative': error, 'text-gray-500': !error }"
          />
        </template>
      </TInput>
      <TInput
        :label="form.password_confirm.name"
        v-model="form.password_confirm.value"
        :error="form.password_confirm.error"
        :errorMessage="form.password_confirm.errorMessage"
        type="password"
        innerClass="text-dark bg-light"
      >
        <template #prepend="{ error }">
          <TIcon
            name="key"
            class="select-none"
            :class="{ 'text-negative': error, 'text-gray-500': !error }"
          />
        </template>
      </TInput>
      <div class="flex flex-col items-stretch justify-center gap-5">
        <TButton
          label="Reset Password"
          type="submit"
          class="w-full rounded-full bg-primary px-2 py-1.5 text-light"
          focustClass="bg-light"
        />
        <router-link
          :to="{ name: 'login' }"
          class="w-full rounded-full px-3 py-1 text-center font-semibold"
        >
          Go to Sign In
        </router-link>
      </div>
    </TCardBody>
    <router-link
      :to="{ name: 'HomePage' }"
      class="absolute left-1/2 top-0 -translate-x-1/2 -translate-y-1/2"
    >
      <TImage
        src="/favicons/baguioseal-animated.svg"
        class="aspect-square h-32 w-32 rounded-full border-8 border-background-accent bg-foreground transition-all hover:scale-105 hover:border-0"
      />
    </router-link>
    <TInnerLoading :active="loading" :text="loadingMessage" isFullScreen />
  </TCard>
</template>

<script setup>
import { ref, inject } from "vue";
import { useRoute, useRouter } from "vue-router";
import { InputField, notify } from "@/scripts";

const $api = inject("$api");
const $router = useRouter();
const $route = useRoute();

const loading = ref(false);
const loadingMessage = ref("");

const form = ref({
  email: new InputField().setName("Email Address").setRules("required|email"),
  password: new InputField()
    .setName("New Password")
    .setRules("required|password"),
  password_confirm: new InputField()
    .setName("Confirm Password")
    .setRules("required|password"),
});

const login = (e) => {
  if (!loading.value && validate()) {
    loading.value = true;
    loadingMessage.value = "Sending reset instructions, please wait...";

    $api
      .post("/password/reset", {
        email: form.value.email.value,
        password: form.value.password.value,
        password_confirmation: form.value.password_confirm.value,
        token: $route.params.token,
      })
      .then((response) => {
        $router.push({ name: "login" });
        notify({
          title: "Success!",
          type: "positive",
          text: response.data.message,
        });
      })
      .finally(() => {
        loading.value = false;
      });
  }
};
const validate = () => {
  Object.values(form.value).forEach((item) => {
    item.validate();
  });
  return !Object.values(form.value).some((item) => item.error);
};
</script>
