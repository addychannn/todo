<template>
  <TCard
    tag="form"
    class="relative w-[95dvw] max-w-[300px] rounded-2xl border border-slate-500/50 bg-slate-100 p-2 text-slate-900 shadow-md"
    @submit.prevent="login"
  >
    <TCardBody class="h-12"> </TCardBody>
    <TCardBody class="">
      <div class="pb-3 text-center text-xl font-semibold">
        {{ $system.product_name }}
      </div>
      <TInput
        :label="form.email.name"
        v-model="form.email.value"
        :error="form.email.error"
        :errorMessage="form.email.errorMessage"
        innerClass="text-dark bg-light"
        autocorrect="off"
        autocapitalize="none"
        autocomplete="off"
      >
        <template #prepend>
          <TIcon name="account_circle" class="select-none text-gray-500" />
        </template>
      </TInput>
      <TInput
        :label="form.password.name"
        v-model="form.password.value"
        :error="form.password.error"
        :errorMessage="form.password.errorMessage"
        :type="showPass ? 'text' : 'password'"
        innerClass="text-dark bg-light"
      >
        <template #prepend>
          <TIcon name="lock" class="select-none text-gray-500" />
        </template>
        <template #append>
          <TIcon
            :name="showPass ? 'visibility_off' : 'visibility'"
            class="cursor-pointer select-none"
            @click="showPass = !showPass"
          />
        </template>
      </TInput>
      <div class="flex flex-col items-stretch justify-center gap-3">
        <router-link
          :to="{ name: 'forgot-password' }"
          class="w-full rounded-full px-3 py-1 text-center font-semibold leading-tight text-primary"
        >
          Forgot Password?
        </router-link>
        <TButton
          label="Login"
          type="submit"
          class="w-full rounded-full bg-primary px-2 py-1.5 uppercase text-light"
          focustClass="bg-light"
        />
        <div class="flex items-center justify-center gap-1 text-sm">
          <span class="font-semibold leading-tight">
            Don't have an account?
          </span>
          <router-link
            :to="{ name: 'register' }"
            class="rounded-full text-center font-bold leading-tight text-primary"
          >
            Sign Up
          </router-link>
        </div>
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
import { onMounted, ref, reactive } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useAuthStore } from "@/stores";
import { InputField } from "@/scripts";

const authStore = useAuthStore();
const $router = useRouter();
const $route = useRoute();

const showPass = ref(false);
const loading = ref(false);
const loadingMessage = ref("");

const form = reactive({
  email: new InputField().setName("Username/Email"),
  password: new InputField().setName("Password"),
});

const login = (e) => {
  if (!loading.value && validate()) {
    loading.value = true;
    loadingMessage.value = "Logging in, please wait...";
    authStore
      .login({
        email: form.email.value,
        password: form.password.value,
      })
      .then((response) => {
        $router.push($route.query.redirect || { name: "HomePage" });
      })
      .catch((error) => {
        form.password.reset();
      })
      .finally(() => {
        loading.value = false;
      });
  }
};
const validate = () => {
  form.email.validate("required");
  form.password.validate("required|minLength:8");
  return !(form.email.error || form.password.error);
};
onMounted(() => {});
</script>
