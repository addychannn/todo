<template>
  <div
    v-if="!!user"
    class="relative flex min-h-full max-w-full flex-auto flex-col rounded-2xl"
  >
    <div class="flex items-center gap-1 px-2">
      <div class="flex-auto pb-5 text-5xl font-bold">User</div>
    </div>
    <div class="flex items-center gap-1 px-2">
      <TButton
        class="rounded-lg px-2 py-1 text-start"
        @click="
          hasHistory ? $router.go(-1) : $router.push({ name: 'settings-users' })
        "
      >
        <div class="pointer-events-none flex items-center">
          <TIcon name="arrow_left" />
          <div class="flex-auto">Back</div>
        </div>
      </TButton>
      <div class="flex-auto"></div>
    </div>
    <div class="flex-auto gap-2 px-2 py-1">
      <div class="grid gap-4">
        <div class="max-w-5xl rounded-xl border border-foreground/25 p-2">
          <div class="text-xl font-semibold">Profile</div>
          <div>
            <div class="table w-full">
              <div class="table-row-group border border-dark">
                <InfoItem
                  label="Photo"
                  @click="openEditor('avatar')"
                  :hoverable="$guard.can(['users_edit-profile'])"
                >
                  <div class="flex items-center">
                    <div class="flex-auto text-sm font-medium">
                      Upload/Select profile picture
                    </div>
                    <TImage
                      :src="user.profile?.image?.thumbnails.small"
                      class="aspect-square w-16 rounded-xl border-2 border-gray-400 bg-light shadow-md after:absolute after:inset-1 after:rounded-full after:shadow-[0_0_0_9999px_rgba(0,0,0,0.3)]"
                    >
                      <template #error>
                        <div class="flex w-full items-center justify-center">
                          <TIcon
                            name="account_circle"
                            class="text-gray-500"
                            size="xl"
                          />
                        </div>
                      </template>
                    </TImage>
                  </div>
                </InfoItem>
                <InfoItem
                  label="Full Name"
                  @click="openEditor('name')"
                  :hoverable="$guard.can(['users_edit-profile'])"
                >
                  <div class="flex items-center">
                    <div class="grid flex-auto grid-cols-2 items-center">
                      <div>
                        {{ user.profile?.full_name }}
                      </div>
                      <div v-if="!!user.profile?.nickname" class="flex-auto">
                        <div class="flex items-center text-sm">
                          <span
                            class="mr-2 text-sm font-semibold text-gray-600 dark:text-gray-300"
                          >
                            Nickname:
                          </span>
                          {{ user.profile?.nickname }}
                        </div>
                      </div>
                    </div>
                    <div class="flex items-center justify-center p-1">
                      <TIcon name="chevron_right" />
                    </div>
                  </div>
                </InfoItem>
                <InfoItem
                  label="Birthdate"
                  @click="openEditor('birthdate')"
                  :hoverable="$guard.can(['users_edit-profile'])"
                >
                  <div class="flex items-center">
                    <div class="grid flex-auto grid-cols-2 items-center">
                      <div>
                        {{ Helpers.formatDate(user.profile?.birthdate) }}
                      </div>
                      <div class="flex-auto">
                        <div class="group flex items-center text-sm">
                          <span
                            class="mr-2 text-sm font-semibold text-gray-600 dark:text-gray-300"
                          >
                            Age
                          </span>
                          <span>
                            {{ age?.years ?? 0 }}
                          </span>
                          <span
                            class="opacity-0 transition md:group-hover:opacity-100"
                          >
                            <span class="px-1">Years</span>
                            <span>{{ age?.months ?? 0 }}</span>
                            <span class="px-1">Months</span>
                            <span>{{ age?.days ?? 0 }}</span>
                            <span class="px-1">Days</span>
                          </span>
                        </div>
                      </div>
                    </div>
                    <div class="flex items-center justify-center p-1">
                      <TIcon name="chevron_right" />
                    </div>
                  </div>
                </InfoItem>
                <InfoItem
                  label="Gender"
                  @click="openEditor('gender')"
                  :hoverable="$guard.can(['users_edit-profile'])"
                >
                  <div class="flex items-center">
                    <div class="flex-auto">
                      {{ user.profile?.gender?.name }}
                    </div>
                    <div class="flex items-center justify-center p-1">
                      <TIcon name="chevron_right" />
                    </div>
                  </div>
                </InfoItem>
                <InfoItem
                  label="Address"
                  @click="openEditor('address')"
                  :hoverable="$guard.can(['users_edit-profile'])"
                >
                  <div class="flex items-center">
                    <div class="flex-auto">
                      <template
                        v-for="address in user.profile?.addresses.filter(
                          (add) => add.isMain
                        )"
                        :key="address.id"
                      >
                        <div class="font-semibold">{{ address.full }}</div>
                      </template>
                      <template
                        v-for="address in user.profile?.addresses.filter(
                          (add) => !add.isMain
                        )"
                        :key="address.id"
                      >
                        <div class="flex items-center gap-1 text-sm italic">
                          <TIcon name="arrow_right_alt" size="xs" />
                          {{ address.full }}
                        </div>
                      </template>
                    </div>
                    <div class="flex items-center justify-center p-1">
                      <TIcon name="chevron_right" />
                    </div>
                  </div>
                </InfoItem>
              </div>
            </div>
          </div>
          <div></div>
        </div>

        <div class="max-w-5xl rounded-xl border border-foreground/25 p-2">
          <div class="flex items-center">
            <div
              class="flex flex-auto items-center justify-start gap-2 text-xl font-semibold"
            >
              <div>Account</div>
              <div class="flex items-center">
                <TIcon
                  :name="!!user.verified ? 'verified' : 'new_releases'"
                  type="filled"
                  :class="!!user.verified ? 'text-positive' : 'text-warning'"
                />
                <TToolTip class="max-w-[12rem] text-center text-xs" arrow>
                  Account {{ !!user.verified ? "verified" : "unverified" }}
                </TToolTip>
              </div>
            </div>
            <div class="flex items-center justify-center gap-2 px-2 py-1">
              <div
                v-if="!user.verified && $guard.can(['users_change-status'])"
                class="flex items-center justify-center"
              >
                <TButton
                  label="Verify account"
                  class="rounded-lg border border-foreground/25 bg-light bg-glossy py-1 pl-1 pr-2 text-dark shadow-lg"
                  focusClass="bg-dark transition"
                  @click="openEditor('verify')"
                />
                <TToolTip class="max-w-[12rem] text-center text-xs" arrow>
                  Verify account allowing user to skip any verification process.
                </TToolTip>
              </div>
              <div class="flex items-center justify-center gap-2">
                <TToggle
                  v-if="$guard.can(['users_change-status'])"
                  :modelValue="user.active"
                  :label="`${user.active ? 'Deactive' : 'Activate'} account!`"
                  @click="openEditor('toggle')"
                />
                <span v-else class="font-semibold">
                  Account is {{ user.active ? "active" : "deactived" }}!
                </span>
              </div>
            </div>
          </div>
          <div>
            <div class="table w-full">
              <div class="table-row-group">
                <InfoItem
                  label="Username"
                  @click="openEditor('username')"
                  :hoverable="$guard.can(['users_edit-account'])"
                >
                  <div class="flex items-center">
                    <div class="flex-auto">
                      {{ user.username }}
                    </div>
                    <div class="flex items-center justify-center p-1">
                      <TIcon name="chevron_right" />
                    </div>
                  </div>
                </InfoItem>
                <InfoItem
                  label="Email"
                  @click="openEditor('email')"
                  :hoverable="$guard.can(['users_edit-account'])"
                >
                  <div class="flex items-center">
                    <div class="flex-auto">
                      {{ user.email }}
                    </div>
                    <div class="flex items-center justify-center p-1">
                      <TIcon name="chevron_right" />
                    </div>
                  </div>
                </InfoItem>
              </div>
            </div>
          </div>
          <div></div>
        </div>

        <div class="max-w-5xl rounded-xl border border-foreground/25 p-2">
          <div class="text-xl font-semibold">Security</div>
          <div>
            <div class="table w-full">
              <div class="table-row-group">
                <InfoItem
                  label="Password"
                  @click="openEditor('password')"
                  :hoverable="$guard.can(['users_edit-account'])"
                >
                  <div class="flex items-center">
                    <div class="flex-auto text-sm">
                      You can change the password here!
                    </div>
                    <div class="flex items-center justify-center p-1">
                      <TIcon name="chevron_right" />
                    </div>
                  </div>
                </InfoItem>
                <InfoItem
                  label="Roles & Permissions"
                  @click="openEditor('permissions')"
                  :hoverable="
                    $guard.can([
                      'users_edit-permission',
                      'users_give-direct-permissions',
                    ])
                  "
                >
                  <div class="flex items-center">
                    <div class="items-centerr flex flex-auto gap-1 text-sm">
                      <template v-for="role in user.roles" :key="role.id">
                        <div
                          class="rounded-lg border border-foreground/25 bg-light px-2 py-0.5 text-dark"
                        >
                          {{ role.name }}
                        </div>
                      </template>
                    </div>
                    <div class="flex items-center justify-center p-1">
                      <TIcon name="chevron_right" />
                    </div>
                  </div>
                </InfoItem>
              </div>
            </div>
          </div>
          <div></div>
        </div>
      </div>
    </div>

    <TDialog
      v-if="
        $guard.can([
          'users_edit-profile',
          'users_edit-account',
          'users_change-status',
          'users_edit-permission',
          'users_give-direct-permissions',
        ])
      "
      v-model="editor.show"
      persistent
    >
      <component
        :is="components[editor.type]"
        :user="user"
        v-on="comOptions[editor.type].events ?? {}"
        v-bind="comOptions[editor.type].bindings ?? {}"
        @update:user="
          (val) =>
            onUserUpdate(val, comOptions[editor.type].bindings.closeEditor)
        "
        @close="editor.show = false"
      />
    </TDialog>
  </div>
</template>

<script setup>
import { ref, computed, reactive, defineAsyncComponent, inject } from "vue";
import { useGuard } from "@/plugins/composables";
import { Helpers } from "@/scripts";

import InfoItem from "./infoItem.vue";

const $guard = useGuard();

const components = {
  avatar: defineAsyncComponent(() => import("./avatar.vue")),
  email: defineAsyncComponent(() => import("./email.vue")),
  username: defineAsyncComponent(() => import("./username.vue")),
  birthdate: defineAsyncComponent(() => import("./birthdate.vue")),
  password: defineAsyncComponent(() => import("./password.vue")),
  name: defineAsyncComponent(() => import("./name.vue")),
  gender: defineAsyncComponent(() => import("./gender.vue")),
  verify: defineAsyncComponent(() => import("./verify.vue")),
  toggle: defineAsyncComponent(() => import("./toggle.vue")),
  permissions: defineAsyncComponent(() => import("./permissions.vue")),
  address: defineAsyncComponent(() => import("./address.vue")),
};

const comOptions = reactive({
  avatar: {
    bindings: {
      closeEditor: false,
      api: computed(() => `/users/avatar/${props.user.id}`),
    },
    events: {},
  },
  email: {
    bindings: {
      closeEditor: true,
      api: computed(() => `/users/email/${props.user.id}`),
      note: null,
    },
    events: {},
  },
  username: {
    bindings: {
      closeEditor: true,
      api: computed(() => `/users/username/${props.user.id}`),
      note: null,
    },
    events: {},
  },
  birthdate: {
    bindings: {
      closeEditor: true,
      api: computed(() => `/users/birthdate/${props.user.id}`),
    },
    events: {},
  },
  password: {
    bindings: {
      closeEditor: true,
      note: null,
    },
    events: {},
  },
  name: {
    bindings: {
      closeEditor: true,
      api: computed(() => `/users/name/${props.user.id}`),
    },
    events: {},
  },
  gender: {
    bindings: {
      closeEditor: true,
      api: computed(() => `/users/gender/${props.user.id}`),
    },
    events: {},
  },
  verify: {
    bindings: {
      closeEditor: true,
    },
    events: {},
  },
  toggle: {
    bindings: {
      closeEditor: true,
    },
    events: {},
  },
  permissions: {
    bindings: {
      closeEditor: true,
    },
    events: {
      autoClose: (val) => {
        comOptions.permissions.bindings.closeEditor = val;
      },
    },
  },
  address: {
    bindings: {
      closeEditor: false,
      api: computed(() => `/users/address/${props.user.id}`),
    },
    events: {},
  },
});

const props = defineProps({
  user: Object,
});

const emit = defineEmits(["update:user"]);

const editor = reactive({
  show: false,
  type: "",
});

const age = computed(() => Helpers.computeAge(props.user.profile?.birthdate));
const toggle = ref(false);

const hasHistory = computed(() => {
  return window.history.length > 2;
});

const openEditor = (type) => {
  const profile = ["avatar", "birthdate", "name", "gender", "address"];
  const account = ["email", "username", "password"];
  const status = ["verify", "toggle"];
  const permissions = ["permissions"];

  const allow =
    $guard.can([]) ||
    (profile.includes(type) && $guard.can("users_edit-profile")) ||
    (account.includes(type) && $guard.can("users_edit-account")) ||
    (status.includes(type) && $guard.can("users_change-status")) ||
    (permissions.includes(type) &&
      $guard.can(["users_edit-permission", "users_give-direct-permissions"]));

  if (allow) {
    editor.type = type;
    editor.show = true;
  }
};

const onUserUpdate = (user, closeEditor) => {
  emit("update:user", user);
  if (closeEditor) {
    editor.show = false;
  }
};
</script>
