<template>
  <div class="relative rounded-md">
    <div class="flex flex-col justify-start gap-2">
      <ProfileItem
        label="Username"
        :value="user.username"
        class="select-none"
        :class="{ 'cursor-pointer': $guard.can('self_update-account') }"
        :icon="$guard.can('self_update-account') ? 'edit' : null"
        @click="openModal('username')"
      ></ProfileItem>
      <ProfileItem
        label="Email"
        :value="user.email"
        class="select-none"
        :class="{ 'cursor-pointer': $guard.can('self_update-account') }"
        :icon="$guard.can('self_update-account') ? 'edit' : null"
        @click="openModal('email')"
      ></ProfileItem>
      <ProfileItem
        label="Verified"
        :value="Helpers.formatDate(user.verified)"
        :icon="!!user.verified ? 'check' : null"
        class="select-none border-none"
        :class="[!user.verified && 'cursor-pointer']"
        :disableFocus="!!user.verified"
      >
      </ProfileItem>
    </div>
    <TDialog
      v-if="$guard.can('self_update-account')"
      v-model="modal.show"
      persistent
    >
      <component
        :is="components[modal.type]"
        :user="user"
        @update:user="onUpdate"
        v-bind="comOptions[modal.type].bindings"
        @close="modal.show = false"
      />
    </TDialog>
  </div>
</template>
<script setup>
import { computed, defineAsyncComponent, inject, ref } from "vue";
import { useVModel } from "@vueuse/core";
import { useRouter } from "vue-router";
import { useGuard } from "@/plugins/composables";
import { Helpers } from "@/scripts";

const router = useRouter();
const $guard = useGuard();

const ProfileItem = defineAsyncComponent(() => import("./profileItem.vue"));

const components = {
  username: defineAsyncComponent(() =>
    import("@/pages/settings/users/editor/username.vue")
  ),
  email: defineAsyncComponent(() =>
    import("@/pages/settings/users/editor/email.vue")
  ),
};

const comOptions = {
  username: {
    bindings: {
      api: "/user/account/username",
      note: "Changing you username will log you out of your current session.",
    },
  },
  email: {
    bindings: {
      api: "/user/account/email",
      note: "Updating your email will invalidate your account, you will have to go through the email verification process again.",
    },
  },
};

const props = defineProps({
  modelValue: Object,
});

const emit = defineEmits(["update:modelValue"]);

const user = useVModel(props, "modelValue", emit);

const maskedUName = computed(() => {
  let regex = /\b(\w{2})(\w+)(\w)\b/g;
  return user.value.username.replace(
    regex,
    (_, first, middle, last) => `${first}${"*".repeat(middle.length)}${last}`
  );
});

const modal = ref({
  show: false,
  type: null,
});

const openModal = (type) => {
  if (!!user.value.verified && $guard.can("self_update-account")) {
    modal.value.show = true;
    modal.value.type = type;
  }
};

const onUpdate = (val) => {
  modal.value.show = false;
  user.value.reset();
  router.push({ name: "login" });
};
</script>
