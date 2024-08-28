<template>
  <TCard
    class="relative max-h-[95dvh] w-[95dvw] max-w-sm bg-background-accent text-foreground"
  >
    <TCardHeader class="bg-page-background">
      <TCardTitle> Password </TCardTitle>
      <TButton
        icon="close"
        iconSize="sm"
        class="aspect-square rounded-full p-0.5"
        :disabled="generating"
        @click="emit('close')"
      />
    </TCardHeader>
    <TCardBody>
      <div
        class="mb-4 rounded-lg border border-warning bg-warning/75 p-2 text-sm text-dark"
      >
        <span class="font-semibold">Note: </span> Changing you password will log
        you out of your current session.
      </div>
      <TInput
        :label="editor.current.name"
        v-model="editor.current.value"
        :error="editor.current.error"
        :errorMessage="editor.current.errorMessage"
        type="password"
        innerClass="bg-light text-dark"
        :disabled="generating"
        @keyup.enter="savePassword"
      />
      <TInput
        :label="editor.new.name"
        v-model="editor.new.value"
        :error="editor.new.error"
        :errorMessage="editor.new.errorMessage"
        type="password"
        innerClass="bg-light text-dark"
        :disabled="generating"
        @keyup.enter="savePassword"
      />
      <TInput
        :label="editor.confirm.name"
        v-model="editor.confirm.value"
        :error="editor.confirm.error"
        :errorMessage="editor.confirm.errorMessage"
        type="password"
        innerClass="bg-light text-dark"
        :disabled="generating"
        @keyup.enter="savePassword"
      />
    </TCardBody>
    <TCardFooter class="flex items-center justify-end gap-1">
      <TButton
        label="Save"
        class="rounded-full bg-primary bg-glossy px-3 py-1 text-light"
        :disabled="generating"
        @click="savePassword"
      />
      <TButton
        label="Cancel"
        class="rounded-full px-3 py-1"
        :disabled="generating"
        @click="emit('close')"
      />
    </TCardFooter>
    <TInnerLoading :active="loading" :text="loadingMessage" />
  </TCard>
</template>

<script setup>
import { inject, ref } from "vue";
import { InputField, notify } from "@/scripts";
import { useAuthStore } from "@/stores";
import { useRouter } from "vue-router";

const $api = inject("$api");
const authStore = useAuthStore();
const router = useRouter();

const emit = defineEmits(["close"]);

const loading = ref(false);
const loadingMessage = ref("");
const generating = ref(false);
const editor = ref({
  current: new InputField()
    .setName("Current Password")
    .setRules("required|password"),
  new: new InputField().setName("New Password").setRules("required|password"),
  confirm: new InputField()
    .setName("Confirm Password")
    .setRules("required|password"),
});

const savePassword = () => {
  if (validate()) {
    loading.value = true;
    loadingMessage.value = "Updating password, please wait...";

    $api
      .patch(`/user/password`, {
        current: editor.value.current.value,
        new: editor.value.new.value,
        confirm: editor.value.confirm.value,
      })
      .then((response) => {
        authStore.reset();
        router.push({ name: "login" });
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
  Object.values(editor.value).forEach((item) => {
    item.validate();
  });

  return !Object.values(editor.value).some((item) => item.error);
};
</script>
