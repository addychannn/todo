<template>
  <TFormWizardTab v-model:fields="fields">
    <template #default="{ editor }">
      <div
        v-for="role in roles"
        :key="role.id"
        class="relative flex cursor-pointer select-none items-center rounded-lg border border-foreground/25 px-3 py-1 transition hover:scale-[1.01]"
        :class="[
          isRoleSelected(editor, role) && 'bg-primary bg-glossy text-light',
        ]"
        role="checkbox"
        :aria-checked="isRoleSelected(editor, role)"
        @click="selectRole(editor, role)"
      >
        <FocusHelper color="bg-foreground" />
        <div class="flex-auto">
          {{ role.name }}
        </div>
        <TIcon
          v-if="isRoleSelected(editor, role)"
          name="check_circle"
          size="sm"
        />
      </div>
    </template>
  </TFormWizardTab>
</template>

<script setup>
import { inject, onMounted, ref } from "vue";
import { Helpers, InputField } from "@/scripts";

const $api = inject("$api");
const fields = ref({
  roles: new InputField([]).setName("Roles").setRules("required"),
});
const roles = ref([]);

const loadRoles = () => {
  $api
    .get("/users/roles")
    .then((response) => {
      roles.value = response.data.data;
    })
    .finally(() => {});
};

const isRoleSelected = (editor, role) => {
  return editor.roles.value.find((item) => item.id == role.id);
};

const selectRole = (editor, role) => {
  if (isRoleSelected(editor, role)) {
    editor.roles.setValue(
      editor.roles.value.filter((item) => item.id != role.id)
    );
  } else {
    editor.roles.value.push(role);
  }
};

onMounted(() => {
  loadRoles();
});
</script>
