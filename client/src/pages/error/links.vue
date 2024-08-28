<template>
  <TCard class="mb-2 bg-slate-100 dark:bg-slate-900">
    <TCardBody class="flex min-w-max justify-center gap-1 text-xs">
      <template
        v-for="(link, index) in visibleLinks"
        :key="`nav_link_${link.label}`"
      >
        <slot :link="link" :index="index" :total="visibleLinks.length">
          <TButton
            :icon="link.icon"
            iconSize="sm"
            :label="link.label"
            :to="link.to ?? null"
            @click="link.action ? link.action() : null"
            v-bind="link.bindings ?? {}"
            class="rounded-md px-3 py-1"
          />
          <div
            v-if="index != visibleLinks.length - 1"
            class="border-l border-slate-700/25"
          />
        </slot>
      </template>
    </TCardBody>
  </TCard>
</template>
<script setup>
import { computed, ref } from "vue";
import { useAuthStore } from "@/stores";

const authStore = useAuthStore();

const props = defineProps({
  links: {
    type: Object,
    default: [],
  },
});

const defaultLinks = ref([
  {
    label: "Home",
    to: { name: "HomePage" },
    icon: "home",
    hidden: false,
  },
  {
    label: "Profile",
    icon: "person",
    to: { name: "profile" },
    hidden: !authStore.isLoggedIn,
  },
  {
    label: "Login",
    to: { name: "login" },
    icon: "home",
    hidden: authStore.isLoggedIn,
  },
  {
    label: "Logout",
    action: () => authStore.logout(),
    icon: "logout",
    hidden: !authStore.isLoggedIn,
  },
]);

const visibleLinks = computed(() => {
  let tmp = [...defaultLinks.value];
  tmp.splice(2, 0, ...props.links);

  return tmp.filter((item) => !item.hidden);
});
</script>
