<template>
  <LSLayout class="!relative">
    <PageContainer>
      <Page
        class="bg-dots bg-dots-animated flex flex-auto items-center justify-center"
      >
        <router-view v-slot="{ Component }">
          <transition
            enterFromClass="opacity-0"
            enterToClass="opacity-100"
            enterActiveClass="transition-opacity duration-300 delay-300 !fixed !top-1/2 !left-1/2 !-translate-y-1/2 !-translate-x-1/2"
            leaveFromClass="opacity-100"
            leaveToClass="opacity-0"
            leaveActiveClass="transition-opacity duration-300 !fixed !top-1/2 !left-1/2 !-translate-y-1/2 !-translate-x-1/2"
          >
            <component :is="Component"> </component>
          </transition>
        </router-view>
      </Page>
      <div class="flex items-center justify-center fixed right-7 top-7">
        <ThemeToggle class="w-10 aspect-square rounded-full" />
      </div>
    </PageContainer>
    <div
      class="pointer-events-none absolute bottom-0 right-0 select-none text-[11px] italic leading-none"
    >
      <div class="rounded-tl-lg bg-slate-900 px-2 leading-none text-slate-100">
        <span class="font-semibold"> Powered By: </span>
        Management Information Technology Division
      </div>
    </div>
  </LSLayout>
</template>

<script setup>
import LSLayout from "./lockScreenLayout.vue";
</script>
