<template>
  <div>
    <div class="rolesWrapper relative grid" ref="rolesWrapper">
      <div v-for="role in roles" :key="role.id" class="p-1">
        <div
          class="relative flex h-11 select-none items-center gap-2 overflow-hidden rounded-xl border border-foreground/25 bg-glossy px-1 text-sm shadow-md transition"
          :class="[
            isSelected(role) && 'bg-primary text-light',
            $guard.can('users_edit-permission') &&
              'cursor-pointer  hover:scale-[1.02]',
          ]"
          @click="selectRole(role)"
        >
          <FocusHelper
            color="bg-foreground"
            :disabled="!$guard.can('users_edit-permission')"
          />
          <TIcon
            :name="
              isSelected(role)
                ? 'check_circle_outline'
                : 'radio_button_unchecked'
            "
            size="sm"
            :class="[
              !$guard.can('users_edit-permission') &&
                !isSelected(role) &&
                'scale-0',
            ]"
          />
          <div class="line-clamp-2 flex items-center">
            {{ role.name }}
          </div>
        </div>
      </div>
      <SizeObserver @resize="onResize" />
    </div>
  </div>
</template>

<script setup>
import { computed, inject, onMounted, ref } from "vue";
import { useGuard } from "@/plugins/composables";

const $api = inject("$api");
const $guard = useGuard();

const props = defineProps({
  roles: Array,
  selected: Array,
  loading: {
    type: Boolean,
    default: false,
  },
  loadingMessage: {
    type: String,
    default: "",
  },
});

const emit = defineEmits([
  "update:loading",
  "update:loadingMessage",
  "update:roles",
  "update:selected",
]);

const _loading = computed({
  get: () => props.loading,
  set: (val) => emit("update:loading", val),
});

const _loadingMessage = computed({
  get: () => props.loading,
  set: (val) => emit("update:loadingMessage", val),
});

const selectedRoles = computed({
  get: () => props.selected,
  set: (val) => emit("update:selected", val),
});

const rolesWrapper = ref(null);

const onResize = (size) => {
  let cols = Math.floor(size.width / 200);
  rolesWrapper.value.style.setProperty("--columns", cols);
};

const loadRoles = () => {
  _loading.value = true;
  _loadingMessage.value = "Loading roles, please wait...";

  $api
    .get(`/users/roles`)
    .then((response) => {
      emit("update:roles", response.data.data);
      let tmp = props.selected.map((role) => role.id);
      selectedRoles.value = response.data.data.filter(
        (role) => tmp.indexOf(role.id) > -1
      );
    })
    .finally(() => {
      _loading.value = false;
    });
};

const isSelected = (role) => {
  return !!props.selected.find((item) => item.id == role.id);
};

const selectRole = (role) => {
  if ($guard.can(["users_edit-permission"])) {
    if (role.name == $guard.super) {
      selectedRoles.value = [role];
    } else {
      if (isRoleSelected(role)) {
        selectedRoles.value = selectedRoles.value.filter(
          (item) => item.id != role.id
        );
      } else {
        selectedRoles.value.push(role);
        selectedRoles.value = selectedRoles.value.filter(
          (item) => item.name != $guard.super
        );
      }
    }
  }
};
const isRoleSelected = (role) => {
  return !!selectedRoles.value.find((item) => item.id == role.id);
};

onMounted(() => {
  if (props.roles.length <= 0) {
    loadRoles();
  }
});
</script>

<style scoped lang="scss">
.rolesWrapper {
  grid-template-columns: repeat(var(--columns), 1fr);
}
</style>
