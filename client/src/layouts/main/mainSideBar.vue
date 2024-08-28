<template>
  <aside
    class="group/main mx-2 mb-2 flex flex-shrink-0 flex-grow-0 basis-auto flex-col rounded-2xl border border-foreground/50 bg-background bg-opacity-75 text-foreground shadow-md backdrop-blur-sm transition-all duration-75"
    :class="[isCollpsed && 'w-12', !isCollpsed && 'w-64']"
    ref="sbar"
  >
    <div class="mb-2 flex justify-center py-2">
      <TImage
        :src="
          authStore.profile?.image?.thumbnails.medium ??
          '/favicons/baguioseal.svg'
        "
        class="aspect-square w-10 origin-[top_center] rounded-full transition-all duration-300 group-[.w-64]/main:scale-[2.25]"
      />
    </div>
    <div
      class="relative top-0 mb-2 flex min-h-[1.5rem] justify-end transition-all duration-300 group-[.w-64]/main:-top-14"
    >
      <button
        v-if="!hideCollapse"
        class="group w-12 outline-0 transition-all group-[.w-64]/main:rotate-180"
        @click="isCollpsed = !isCollpsed"
      >
        <TIcon
          name="keyboard_double_arrow_right"
          class="transition-all group-hover:text-primary group-hover:lg:translate-x-3"
        />
      </button>
    </div>
    <div
      class="TScroll flex-auto overflow-y-auto overflow-x-hidden !scrollbar-w-0.5"
    >
      <template v-for="group in _menu" :key="`menu-${group.title}`">
        <div
          v-if="!group.hidden"
          class="mb-3 transition-all"
          :class="[isCollpsed ? 'px-[0.375rem]' : 'px-2']"
        >
          <h3
            class="mb-1 whitespace-nowrap text-sm font-bold uppercase text-foreground/50 opacity-0 transition-all duration-300 group-[.w-64]/main:ml-2 group-[.w-64]/main:opacity-100"
          >
            {{ visibleLabel ? group.title : "&nbsp;" }}
          </h3>
          <template
            v-for="link in group.links"
            :key="`menu-${menu.title}-${link.label}`"
          >
            <template v-if="!link.hidden">
              <button
                v-if="!!link.sub && !link.to"
                class="mb-1 flex w-full items-center rounded-md py-1 text-start transition-all group-[.w-64]/main:rounded-xl hover:!bg-foreground/50"
                @click="link.collapse = !link.collapse"
              >
                <div
                  class="flex h-8 w-8 items-center justify-center px-1 transition-all duration-300 group-[.w-64]/main:mr-2"
                >
                  <TIcon :name="link.icon ?? 'label'" />
                </div>
                <div
                  class="flex-auto whitespace-nowrap font-semibold opacity-0 transition-all duration-300 group-[.w-64]/main:opacity-100"
                >
                  {{ visibleLabel ? link.label : "" }}
                </div>
                <template v-if="!!link.sub?.length">
                  <div
                    @click.prevent.stop="link.collapse = !link.collapse"
                    class="box-border flex h-full items-center justify-center px-2 focus-within:outline"
                  >
                    <input
                      type="checkbox"
                      v-model="link.collapse"
                      class="peer appearance-none"
                    />
                    <TIcon
                      name="expand_more"
                      class="rotate-90 transform transition-transform delay-300 peer-checked:rotate-0 peer-checked:delay-[0ms]"
                    />
                  </div>
                </template>
              </button>
              <router-link
                v-else
                :to="link.to"
                class="mb-1 flex items-center rounded-md py-1 transition-all group-[.w-64]/main:rounded-xl hover:!bg-foreground/25"
              >
                <div
                  class="flex h-8 w-8 items-center justify-center px-1 transition-all duration-300 group-[.w-64]/main:mr-2"
                >
                  <TIcon :name="link.icon ?? 'label'" />
                </div>
                <div
                  class="flex flex-auto items-center whitespace-nowrap font-semibold opacity-0 transition-all duration-300 group-[.w-64]/main:opacity-100"
                >
                  <div class="flex-auto">
                    {{ visibleLabel ? link.label : "" }}
                  </div>
                  <template v-if="!!link.sub?.length">
                    <div
                      @click.prevent.stop="link.collapse = !link.collapse"
                      class="box-border flex h-full items-center justify-center border-l px-2 focus-within:outline"
                    >
                      <input
                        :id="Helpers.uniqid('sidenav')"
                        type="checkbox"
                        tabindex="-1"
                        v-model="link.collapse"
                        class="peer appearance-none"
                      />
                      <TIcon
                        name="expand_more"
                        class="rotate-90 transform transition-transform delay-300 peer-checked:rotate-0 peer-checked:delay-[0ms]"
                      />
                    </div>
                  </template>
                </div>
                <TToolTip
                  v-if="!visibleLabel"
                  tag="a"
                  arrow
                  position="left"
                  class="cursor-pointer !p-0"
                  pointerEvents
                  transitionName="toLeft"
                  :href="$router.resolve(link.to).href"
                  @click.prevent="$router.push(link.to)"
                >
                  <div
                    class="min-w-[150px] select-none px-2 py-2 font-semibold"
                  >
                    {{ link.label }}
                  </div>
                </TToolTip>
              </router-link>
              <template v-if="!!link.sub">
                <TCollapse
                  v-model="link.collapse"
                  class="border-l border-negative transition-all duration-300 group-[.w-64]/main:ml-7 group-[.w-64]/main:pl-1"
                >
                  <template
                    v-for="sublink in link.sub"
                    :key="`${link.label}_sublink_${sublink.label}`"
                  >
                    <router-link
                      v-if="!sublink.hidden"
                      :to="sublink.to"
                      class="mb-1 flex items-center rounded-md py-1 transition-all group-[.w-64]/main:rounded-xl hover:!bg-foreground/25"
                    >
                      <div
                        class="flex h-8 w-8 items-center justify-center px-1 transition-all duration-300 group-[.w-64]/main:mr-2"
                      >
                        <TIcon :name="sublink.icon ?? 'bookmark'" />
                      </div>
                      <div
                        class="flex-auto whitespace-nowrap font-semibold opacity-0 transition-all duration-300 group-[.w-64]/main:opacity-100"
                      >
                        {{ visibleLabel ? sublink.label : "" }}
                      </div>
                      <TToolTip
                        v-if="!visibleLabel"
                        tag="a"
                        arrow
                        position="left"
                        class="cursor-pointer !p-0"
                        pointerEvents
                        transitionName="toLeft"
                        :href="$router.resolve(sublink.to).href"
                        @click.prevent="$router.push(sublink.to)"
                      >
                        <div
                          class="min-w-[150px] select-none px-2 py-2 font-semibold"
                        >
                          {{ sublink.label }}
                        </div>
                      </TToolTip>
                    </router-link>
                  </template>
                </TCollapse>
              </template>
            </template>
          </template>
        </div>
      </template>
    </div>
    <div class="mb-2">
      <div class="px-2">
        <button
          class="group/logout mb-1 flex w-full items-center rounded-md py-1 text-start transition-all group-[.w-64]/main:rounded-xl hover:bg-negative hover:text-light"
          @click.prevent.stop="authStore.logout()"
        >
          <div
            class="flex h-8 w-8 items-center justify-center px-1 transition-all group-hover/logout:text-light group-[.w-64]/main:mr-2"
          >
            <TIcon name="logout" class="" />
          </div>
          <div
            class="flex-auto whitespace-nowrap font-semibold opacity-0 transition-all duration-300 group-hover/logout:text-light group-[.w-64]/main:opacity-100"
          >
            {{ visibleLabel ? "Logout" : "" }}
          </div>
          <TToolTip
            v-if="!visibleLabel"
            arrow
            position="left"
            class="cursor-pointer !p-0 text-foreground"
            pointerEvents
            transitionName="toLeft"
            @click="authStore.logout()"
          >
            <div class="min-w-[150px] select-none px-2 py-2 font-semibold">
              Logout
            </div>
          </TToolTip>
        </button>
      </div>
    </div>
  </aside>
</template>

<script setup>
import { computed, onMounted, onBeforeUnmount, ref, watch } from "vue";
import { useAuthStore } from "@/stores";
import { useRoute, useRouter } from "vue-router";
import { Helpers } from "@/scripts";

const route = useRoute();
const router = useRouter();
const props = defineProps({
  menu: Object,
  collapsed: {
    type: Boolean,
    default: null,
  },
  hideCollapse: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits([
  "update:collapsed",
  "update:menu",
  "redirect",
  "collapseStart",
  "collapseEnd",
  "expandStart",
  "expandEnd",
  "resizeStart",
  "resizeEnd",
]);

const authStore = useAuthStore();

const sbar = ref(null);
const transitioning = ref(false);
const scrolledDown = ref(false);
const scrollOffset = ref(0);

const _menu = computed({
  get: () => {
    return props.menu;
  },
  set: (val) => {
    emit("update:menu", val);
  },
});

const isCollpsed = computed({
  get: () => props.collapsed,
  set: (val) => {
    transitioning.value = true;
    emit("update:collapsed", val);
    emit("resizeStart", val);
    emit(val ? "collapseStart" : "expandStart");
  },
});

const visibleLabel = computed(() => transitioning.value || !isCollpsed.value);

const onTransitionEnd = (e) => {
  transitioning.value = false;
  emit("collapseEnd");
  emit("expandEnd");
  emit("resizeEnd");
};

const checkActiveSub = () => {
  props.menu.forEach((group) => {
    let tmp = group.links.filter(
      (item) =>
        !!item.sub && !!item.sub.find((sub) => sub.to.name == route.name)
    );
    tmp.forEach((parent) => {
      parent.collapse = true;
    });
  });
};

watch(route, (to, from) => {
  emit("redirect", {
    to: to,
    from: from,
  });
});

onMounted(() => {
  checkActiveSub();
  sbar.value.addEventListener("transitionend", onTransitionEnd);
});

onBeforeUnmount(() => {
  sbar.value.removeEventListener("transitionend", onTransitionEnd);
});
</script>

<style scoped lang="scss">
.router-link-active {
  @apply bg-foreground/10;
}

.router-link-exact-active {
  @apply bg-foreground/20 bg-glossy;
}
</style>
