<template>
  <Page class="bg-background">
    <div
      class="absolute inset-x-0 top-0 h-72 overflow-hidden border-b border-foreground/25 bg-page-background bg-cover bg-fixed bg-center"
      :style="{ backgroundImage: `url('/src/assets/bg/sample.jpg')` }"
    >
      <div
        class="flex h-full w-full justify-center bg-dark bg-opacity-50 pt-16 text-5xl"
      ></div>
    </div>
    <div class="flex justify-stretch">
      <div class="!z-20 flex w-full justify-center py-24">
        <TCard
          class="relative min-h-[28rem] w-[95dvw] max-w-xl border border-foreground/25 !bg-background pt-11 !text-foreground shadow-none md:pt-0"
        >
          <div
            class="absolute inset-x-0 top-0 z-10 mx-3 rounded-b-md rounded-t-xl bg-inherit py-1 shadow-md shadow-foreground/25 dark:shadow-foreground/10 md:relative md:shadow-none"
          >
            <label
              v-if="!$md"
              for="menu_btn"
              class="flex items-center justify-between bg-transparent px-3"
            >
              <div class="px-3 text-lg font-bold">Menu</div>
              <TButton
                v-on-click-outside="[
                  (ev) => {
                    toggleMenu(false);
                  },
                ]"
                id="menu_btn"
                :icon="menu.open ? 'close' : 'menu'"
                class="z-20 rounded-md p-1"
                :iconClass="['transition-transform', menu.open && 'rotate-180']"
                @click="toggleMenu(null)"
              />
            </label>
            <div
              class="overflow-hidden rounded-b-md bg-inherit transition-all duration-300"
              :class="{
                'max-h-0': !$md && !menu.open,
                'max-h-screen': !$md && menu.open,
              }"
            >
              <div
                class="flex flex-col justify-between gap-1 bg-inherit px-3 py-1 text-lg md:flex-row md:items-center [&>button]:!font-semibold"
              >
                <TButton
                  icon="account_circle"
                  label="Profile"
                  focusClass="bg-dark"
                  class="border-l-4 px-3 py-1 md:rounded-t-md md:border-b-4 md:border-l-0"
                  contentClass="!justify-start md:!justify-center"
                  :class="{
                    'border-primary': active == 'profile',
                    'border-transparent': active != 'profile',
                  }"
                  @click="active = 'profile'"
                />
                <TButton
                  icon="settings"
                  label="Account"
                  focusClass="bg-dark"
                  class="border-l-4 px-3 py-1 md:rounded-t-md md:border-b-4 md:border-l-0"
                  contentClass="!justify-start md:!justify-center"
                  :class="{
                    'border-primary': active == 'account',
                    'border-transparent': active != 'account',
                  }"
                  @click="active = 'account'"
                />
                <TButton
                  icon="verified_user"
                  label="Security"
                  focusClass="bg-dark"
                  class="border-l-4 px-3 py-1 md:rounded-t-md md:border-b-4 md:border-l-0"
                  contentClass="!justify-start md:!justify-center"
                  :class="{
                    'border-primary': active == 'security',
                    'border-transparent': active != 'security',
                  }"
                  @click="active = 'security'"
                />
                <TButton
                  icon="timeline"
                  label="Account Activities"
                  focusClass="bg-dark"
                  class="border-l-4 px-3 py-1 md:rounded-t-md md:border-b-4 md:border-l-0"
                  contentClass="!justify-start md:!justify-center"
                  :class="{
                    'border-primary': active == 'activities',
                    'border-transparent': active != 'activities',
                  }"
                  @click="active = 'activities'"
                />
              </div>
            </div>
          </div>
          <TCardBody
            class="relative flex min-h-[25rem] flex-col"
            :class="transitioning && '!h-[25rem] overflow-hidden'"
          >
            <transition
              enter-from-class="opacity-0 blur-md"
              leave-to-class="opacity-0 blur-md"
              enter-active-class="transition duration-300 delay-300 inset-0"
              leave-active-class="transition duration-300 inset-0"
              @before-leave="transitioning = true"
              @after-enter="transitioning = false"
            >
              <component
                :is="modules[active]"
                class="flex-auto"
                :class="{ 'blur-sm': !authStore.verified }"
                v-model="authStore"
              />
            </transition>

            <div
              v-if="!authStore.verified"
              class="absolute inset-x-3 bottom-3 top-0 flex flex-col items-center justify-center gap-2 rounded-2xl bg-light bg-opacity-10 backdrop-blur-sm"
            >
              <div
                class="flex max-w-xs items-center justify-center gap-2 rounded-md border border-foreground/25 bg-light p-2 text-dark shadow-md shadow-foreground/25"
              >
                <TIcon name="info" size="2xl" />
                <div class="font-semibold">
                  Email verification is required to update your profile!
                </div>
              </div>
              <div class="">
                <VerificationLinkSender />
              </div>
            </div>
          </TCardBody>
        </TCard>
      </div>
    </div>
  </Page>
</template>

<script setup>
import { defineAsyncComponent, inject, ref, watch } from "vue";
import { vOnClickOutside } from "@vueuse/components";
import { useAuthStore } from "@/stores";

const VerificationLinkSender = defineAsyncComponent(() =>
  import("./verificationLinkSender.vue")
);

const $screen = inject("$screen");
const $md = $screen.value.greaterOrEqual("md");
const props = defineProps({});
const authStore = useAuthStore();

const transitioning = ref(false);
const menu = ref({
  open: false,
});

const modules = {
  profile: defineAsyncComponent(() => import("./profile.vue")),
  account: defineAsyncComponent(() => import("./account.vue")),
  security: defineAsyncComponent(() => import("./security.vue")),
  activities: defineAsyncComponent(() => import("./activities.vue")),
};

const active = ref("profile");

const toggleMenu = (state = null) => {
  if (state === null) {
    menu.value.open = !menu.value.open;
  } else {
    menu.value.open = state;
  }
};

watch($md, (val) => {
  toggleMenu(false);
});
</script>
