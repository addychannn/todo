<template>
  <Register
    class="!max-h-[80dvh] border border-slate-500/50 bg-slate-100 text-slate-900"
    :modules="{
      profile: { requireAvatar: true },
      avatar: {},
      account: { pConfirm: true, requireEmail: true },
    }"
    api="/auth/register"
    avatarApi="/auth/register"
    @created="router.push({ name: 'login' })"
    :wizardBindings="{
      stepClasses: {
        bgClass: '!bg-light',
        idleLabelClass: '!text-dark',
      },
    }"
  >
    <template #header>
      <TCardBody class="h-16"> </TCardBody>
    </template>
    <template #title="{ title }">
      <div
        class="relative flex items-center justify-center px-3 py-1 text-center font-bold uppercase"
      >
        <div
          class="absolute inset-x-1 top-1/2 z-0 box-border -translate-y-1/2 rounded-full border-t border-dark/25"
        />
        <div class="relative z-10 rounded-md bg-light px-3 py-1 leading-tight">
          {{ title }}
        </div>
      </div>
    </template>
    <template
      #footer="{
        nextPage,
        prevPage,
        currentPage,
        totalPages,
        addUser,
        hideNav,
      }"
    >
      <div
        v-if="!hideNav"
        class="flex items-center justify-evenly gap-2 px-3 py-1"
      >
        <TButton
          label="Previous"
          @click="prevPage"
          :disabled="currentPage <= 0"
          class="rounded-full px-3 py-1"
        />
        <TButton
          :label="currentPage == totalPages - 1 ? 'Save' : 'Next'"
          @click="currentPage == totalPages - 1 ? addUser() : nextPage()"
          class="rounded-full border px-3 py-1 transition"
          :class="[
            currentPage == totalPages - 1
              ? 'border-primary bg-primary bg-glossy text-light'
              : 'border-transparent',
          ]"
        />
      </div>
    </template>
    <template #after>
      <div
        class="flex items-center justify-center gap-1 border-t border-dark/25 py-3 text-sm"
      >
        <span class="font-semibold leading-tight">
          Already have an account?
        </span>
        <router-link
          :to="{ name: 'login' }"
          class="rounded-full text-center font-bold leading-tight text-primary"
        >
          Sign In
        </router-link>
      </div>
      <router-link
        :to="{ name: 'HomePage' }"
        class="absolute left-1/2 top-0 -translate-x-1/2 -translate-y-1/2"
      >
        <TImage
          src="/favicons/baguioseal-animated.svg"
          class="aspect-square h-32 w-32 rounded-full border-8 border-background-accent bg-foreground transition-all hover:scale-105 hover:border-0"
        />
      </router-link>
    </template>
  </Register>
</template>

<script setup>
import { defineAsyncComponent, ref } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();

const Register = defineAsyncComponent(() =>
  import("../settings/users/editor/createUser/index.vue")
);
</script>
