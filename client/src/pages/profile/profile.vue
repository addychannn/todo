<template>
  <div class="relative rounded-md">
    <div class="flex flex-col justify-start gap-2">
      <div class="flex flex-col items-center justify-center gap-1">
        <div
          :role="$guard.can('self_change-avatar') ? 'button' : null"
          aria-label="Select Profile Picture"
          class="group relative flex aspect-square w-[95dvh] max-w-[10rem] select-none items-center justify-center overflow-hidden rounded-full border-4 border-foreground"
          :class="{ 'cursor-pointer': $guard.can('self_change-avatar') }"
          @click="openModal('avatar')"
        >
          <TImage
            :src="user.profile?.image?.thumbnails?.small"
            class="z-0 flex-auto"
          >
            <template #error>
              <TIcon name="account_circle" size="5xl" class="text-gray-400" />
            </template>
          </TImage>
          <template v-if="$guard.can('self_change-avatar')">
            <div
              class="absolute inset-0 z-10 bg-dark/25 opacity-0 transition duration-300 group-hover:opacity-100"
            />
            <div
              class="absolute inset-x-0 bottom-0 z-20 flex-auto bg-dark/50 text-center text-light opacity-100 transition duration-300 md:opacity-0 md:group-hover:opacity-100"
            >
              <TIcon name="photo_camera" />
            </div>
          </template>
        </div>
      </div>
      <div class="grid gap-2">
        <ProfileItem
          label="Full Name"
          :value="user.profile?.full_name ?? 'N/A'"
          class="select-none"
          :class="{ 'cursor-pointer': $guard.can('self_update-profile') }"
          :icon="$guard.can('self_update-profile') ? 'edit' : null"
          @click="openModal('name')"
        />
        <ProfileItem
          label="Birth Date"
          :value="Helpers.formatDate(user.profile?.birthdate, 'N/A')"
          class="select-none"
          :class="{ 'cursor-pointer': $guard.can('self_update-profile') }"
          :icon="$guard.can('self_update-profile') ? 'edit' : null"
          @click="openModal('birthdate')"
        />
        <ProfileItem
          label="Gender"
          :value="user.profile?.gender?.name ?? 'N/A'"
          class="select-none"
          :class="{ 'cursor-pointer': $guard.can('self_update-profile') }"
          :icon="$guard.can('self_update-profile') ? 'edit' : null"
          @click="openModal('gender')"
        />
        <ProfileItem
          label="Address"
          class="select-none !border-none"
          :class="{ 'cursor-pointer': $guard.can('self_update-profile') }"
          :icon="$guard.can('self_update-profile') ? 'edit' : null"
          @click="openModal('address')"
        >
          <div class="grid gap-0.5">
            <template
              v-for="address in user.profile?.addresses"
              :key="address.id"
            >
              <div
                class="items-centr flex gap-1"
                :class="{ 'text-sm font-normal': !address.isMain }"
              >
                <TIcon v-if="!address.isMain" name="arrow_right" size="sm" />
                {{ address.full }}
              </div>
            </template>
          </div>
        </ProfileItem>
      </div>
    </div>
    <TDialog
      v-if="$guard.can(['self_update-profile', 'self_change-avatar'])"
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
import { defineAsyncComponent, inject, ref } from "vue";
import { useVModel } from "@vueuse/core";
import { useGuard } from "@/plugins/composables";
import { Helpers } from "@/scripts";

const ProfileItem = defineAsyncComponent(() => import("./profileItem.vue"));

const $guard = useGuard();

const components = {
  avatar: defineAsyncComponent(() =>
    import("@/pages/settings/users/editor/avatar.vue")
  ),
  name: defineAsyncComponent(() =>
    import("@/pages/settings/users/editor/name.vue")
  ),
  birthdate: defineAsyncComponent(() =>
    import("@/pages/settings/users/editor/birthdate.vue")
  ),
  gender: defineAsyncComponent(() =>
    import("@/pages/settings/users/editor/gender.vue")
  ),
  address: defineAsyncComponent(() =>
    import("@/pages/settings/users/editor/address.vue")
  ),
};
const comOptions = {
  avatar: {
    bindings: {
      api: "/user/image",
    },
  },
  name: {
    bindings: {
      api: "/user/profile/name",
    },
  },
  birthdate: {
    bindings: {
      api: "/user/profile/birthdate",
    },
  },
  gender: {
    bindings: {
      api: "/user/profile/gender",
    },
  },
  address: {
    bindings: {
      api: "/user/profile/address",
    },
  },
};

const props = defineProps({
  modelValue: Object,
});

const emit = defineEmits(["update:modelValue"]);

const user = useVModel(props, "modelValue", emit);

const modal = ref({
  show: false,
  type: null,
});

const openModal = (type) => {
  let permitted = $guard.can(["self_update-profile", "self_change-avatar"]);

  if (!!user.value.verified && permitted) {
    modal.value.show = true;
    modal.value.type = type;
  }
};

const onUpdate = (val) => {
  Object.assign(user.value, val);
  modal.value.show = false;
};
</script>
