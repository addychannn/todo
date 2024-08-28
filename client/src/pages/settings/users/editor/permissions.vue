<template>
  <TCard
    class="relative max-h-[95dvh] w-[95dvw] max-w-2xl bg-background-accent text-foreground"
  >
    <TCardHeader class="bg-page-background">
      <TCardTitle>Change user access</TCardTitle>
      <TButton
        icon="close"
        iconSize="sm"
        class="aspect-square rounded-full p-0.5"
        @click="emit('close')"
      />
    </TCardHeader>
    <TCardHeader class="grid grid-cols-2 items-center !rounded-none !p-0">
      <TButton
        label="Roles"
        @click="selectPermissions = false"
        class="p-2 transition-all"
        :class="[!selectPermissions && 'bg-primary text-light']"
      />
      <TButton
        label="Permissions"
        @click="selectPermissions = true"
        class="p-2 transition-all"
        :class="[selectPermissions && 'bg-primary text-light']"
      />
    </TCardHeader>
    <TCardBody
      class="relative flex min-h-[33dvh] flex-col"
      :class="[(transitioning || loading) && 'overflow-hidden']"
    >
      <transition
        v-bind="{
          ...transitions[selectPermissions ? 'toRight' : 'toLeft'],
          enterActiveClass:
            'transition-all duration-300 absolute top-2 inset-x-3',
          leaveActiveClass:
            'transition-all duration-300 absolute top-2 inset-x-3',
        }"
        @before-leave="transitioning = true"
        @after-enter="transitioning = false"
      >
        <RolesSelection
          v-if="!selectPermissions"
          class="inset-y-2 flex-auto"
          v-model:roles="roles"
          v-model:loading="loading"
          v-model:loadingMessage="loadingMessage"
          v-model:selected="selectedRoles"
        />
        <PermissionsSelection
          v-else
          v-model:selected="selectedPermissions"
          v-model:permissions="permissions"
          v-model:loading="loading"
          v-model:loadingMessage="loadingMessage"
          :roles="selectedRoles"
          class="inset-y-2 flex-auto"
        />
      </transition>
    </TCardBody>
    <TCardFooter class="flex items-center justify-end gap-1">
      <div class="flex flex-auto items-center">
        <div v-if="error" class="flex items-center gap-1 text-sm">
          <span
            class="flex aspect-square items-center rounded-full bg-light leading-none"
          >
            <TIcon name="error" class="text-negative" />
          </span>
          {{ errorMessage }}
        </div>
      </div>
      <TCheckBox
        v-model="autoClose"
        @update:modelValue="(val) => emit('autoClose', val)"
        label="Close on Save"
      />
      <TButton
        label="Save"
        class="rounded-full bg-primary bg-glossy px-3 py-1 text-light"
        @click="savePermissions"
      />
      <TButton
        label="Cancel"
        class="rounded-full px-3 py-1"
        @click="emit('close')"
      />
    </TCardFooter>
    <TInnerLoading :active="loading" :text="loadingMessage" />
  </TCard>
</template>

<script setup>
import { defineAsyncComponent, inject, onMounted, ref } from "vue";
import { transitions, notify } from "@/scripts";

const $api = inject("$api");

const RolesSelection = defineAsyncComponent(() =>
  import("./permissions/roles.vue")
);

const PermissionsSelection = defineAsyncComponent(() =>
  import("./permissions/permissions.vue")
);

const props = defineProps({
  user: Object,
});

const emit = defineEmits(["update:user", "close", "autoClose"]);

const transitioning = ref(false);
const loading = ref(false);
const loadingMessage = ref("");
const error = ref(false);
const errorMessage = ref("");

const roles = ref([]);
const selectedRoles = ref([]);
const selectedPermissions = ref([]);
const permissions = ref([]);
const autoClose = ref(true);

const selectPermissions = ref(false);

const savePermissions = () => {
  if (validate()) {
    loading.value = true;
    loadingMessage.value = "Updating permissions, please wait...";

    $api
      .patch(`/users/permissions/${props.user.id}`, {
        permissions: selectedPermissions.value,
        roles: selectedRoles.value.map((role) => role.id),
      })
      .then((response) => {
        emit("update:user", response.data.data);
        notify({
          title: "Success!",
          type: "positive",
          text: response.data.message,
        });
      })
      .finally(() => {
        loading.value = false;
      });
  }
};

const validate = () => {
  let result = true;
  if (
    selectedRoles.value.length <= 0 &&
    selectedPermissions.value.length <= 0
  ) {
    error.value = true;
    errorMessage.value = "Please select a role and/or permission.";
    result = false;
  }
  return result;
};

onMounted(() => {
  selectedRoles.value = JSON.parse(JSON.stringify(props.user.roles));
  selectedPermissions.value = JSON.parse(
    JSON.stringify(props.user.permissions)
  );
});
</script>
