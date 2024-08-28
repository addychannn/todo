<template>
  <Header
    class="z-50 flex border-b !border-foreground/25 bg-background bg-opacity-75 p-0 py-1 text-foreground shadow-md backdrop-blur-sm transition-colors duration-1000"
    :class="[systemStore.settings.navbar.fixed && 'fixed left-0 top-0 z-10']"
    :style="{ right: `${layoutWidthNoScroll}px` }"
  >
    <SizeObserver @resize="onHeaderResize" />
    <div
      class="flex min-h-full min-w-full flex-col gap-1 px-4 sm:flex-row md:items-center"
    >
      <div class="flex flex-auto items-center">
        <slot name="prepend"></slot>
        <router-link
          :to="{ name: 'HomePage' }"
          class="inline-flex items-center"
        >
          <TImage
            src="/favicons/baguioseal.svg"
            class="aspect-square w-12 rounded-full border-light"
          />
          <span
            class="flex items-center justify-center p-2 text-lg font-semibold md:text-2xl"
          >
            {{ $system.product_name }}
          </span>
        </router-link>
      </div>
      <nav class="flex h-full items-center justify-center md:justify-end">
        <ul class="flex h-full items-center gap-1 [&>li]:h-full">
          <li>
            <TPopover
              v-if="authStore.isLoggedIn"
              :btnClass="[
                'h-10 bg-foreground/10 px-2 py-1 rounded-lg my-1',
                $md && 'min-w-[12rem]',
              ]"
              contentClass="leading-none"
              arrow
            >
              <template #button>
                <div class="pointer-events-none flex items-center gap-2">
                  <TImage
                    v-if="!!authStore.profile?.image"
                    :src="authStore.profile.image.thumbnails.small"
                    class="aspect-square w-6 rounded-full ring-2 ring-light"
                  />
                  <TIcon
                    v-else
                    name="account_circle"
                    class="ring-2 ring-light"
                  />
                  <div v-if="$md" class="text-start">
                    <div
                      class="line-clamp-1 text-sm font-semibold uppercase leading-none"
                    >
                      {{ authStore.profile?.full_name ?? authStore.username }}
                    </div>
                    <div class="text-xs leading-none">
                      {{ authStore.email }}
                    </div>
                  </div>
                </div>
              </template>
              <template #default="{ close, visible }">
                <template v-if="visible">
                  <div class="min-w-[12rem] bg-foreground/25 py-0.5">
                    <div v-if="!$md" class="px-2 py-1 text-start">
                      <div
                        class="line-clamp-1 text-sm font-semibold uppercase leading-none"
                      >
                        {{ authStore.profile?.full_name ?? authStore.username }}
                      </div>
                      <div class="text-xs leading-none">
                        {{ authStore.email }}
                      </div>
                    </div>
                  </div>
                  <TButton
                    class="py-2"
                    :to="{ name: 'profile' }"
                    @click="close()"
                  >
                    <div
                      class="pointer-events-none flex items-center gap-1 py-1"
                    >
                      <div
                        class="flex items-center justify-center border-r border-foreground/25 px-1"
                      >
                        <TIcon name="person" size="sm" />
                      </div>
                      <div class="flex-auto text-start leading-none">
                        Profile
                      </div>
                    </div>
                  </TButton>
                  <TButton
                    class="py-2"
                    :to="{ name: 'settings' }"
                    @click="close()"
                  >
                    <div
                      class="pointer-events-none flex items-center gap-1 py-1"
                    >
                      <div
                        class="flex items-center justify-center border-r border-foreground/25 px-1"
                      >
                        <TIcon name="settings" size="sm" />
                      </div>
                      <div class="flex-auto text-start leading-none">
                        Settings
                      </div>
                    </div>
                  </TButton>
                  <TButton
                    class="w-full py-2"
                    @click="authStore.logout(), close()"
                  >
                    <div
                      class="pointer-events-none flex items-center gap-1 py-1"
                    >
                      <div
                        class="flex items-center justify-center border-r border-foreground/25 px-1"
                      >
                        <TIcon name="logout" size="sm" />
                      </div>
                      <div class="flex-auto text-start leading-none">
                        Logout
                      </div>
                    </div>
                  </TButton>
                  <div class="bg-foreground/25 py-0.5"></div>
                </template>
              </template>
            </TPopover>
            <router-link
              v-else
              :to="{ name: 'login' }"
              icon="login"
              class="relative my-1 inline-flex h-10 items-center overflow-hidden rounded-lg px-3"
              @click="systemStore.toggleFixedNavbar()"
            >
              <div class="pointer-events-none flex items-center gap-1">
                <div class="flex items-center justify-center">
                  <TIcon name="login" />
                </div>
                <div v-if="$md" class="font-semibold leading-tight">Login</div>
              </div>
              <FocusHelper color="bg-foreground" />
            </router-link>
          </li>
          <li>
            <ThemeToggle
              :label="$md ? 'Theme' : null"
              class="h-10 rounded-lg px-3"
            />
          </li>
          <li v-if="!!authStore.pin">
            <TButton
              icon="lock"
              :label="$md ? 'Lock Screen' : null"
              class="my-1 h-10 rounded-lg px-3"
              @click="authStore.lock()"
            />
          </li>
          <li>
            <TButton
              :icon="
                systemStore.settings.navbar.fixed ? 'toggle_on' : 'toggle_off'
              "
              :label="$md ? 'Fixed Nav' : null"
              class="my-1 h-10 rounded-lg px-3"
              @click="systemStore.toggleFixedNavbar()"
            />
          </li>
        </ul>
      </nav>
    </div>
  </Header>
</template>

<script setup>
import { onBeforeUnmount, onMounted, ref, inject } from "vue";
import { useSystemStore, useAuthStore } from "@/stores";
import { useRoute } from "vue-router";

const systemStore = useSystemStore();
const authStore = useAuthStore();
const route = useRoute();

const $screen = inject("$screen");
const $md = $screen.value.greaterOrEqual("md");

const props = defineProps({
  layoutRef: Object,
});

const emit = defineEmits(["resize"]);

const layoutWidthNoScroll = ref(0);
const headerSize = ref({
  width: 0,
  height: 0,
});
const scrollOffset = ref(0);

const onHeaderResize = (size) => {
  headerSize.value = size;

  let body = document.getElementsByTagName("body")[0];
  let layout = props.layoutRef?.$el ?? null;

  layoutWidthNoScroll.value = body.offsetWidth - (layout?.offsetWidth ?? 0);

  emit("resize", size);
};

const onBodyScroll = (e) => {
  scrollOffset.value = e.target.scrollTop;
};

onMounted(() => {
  document.body.addEventListener("scroll", onBodyScroll);
});
onBeforeUnmount(() => {
  document.body.removeEventListener("scroll", onBodyScroll);
});
</script>
