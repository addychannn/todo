<template>
  <TCard
    class="relative max-h-[95dvh] w-[95dvw] max-w-sm bg-background-accent text-foreground"
  >
    <TCardHeader class="bg-page-background">
      <TCardTitle> Username </TCardTitle>
      <TButton
        icon="close"
        iconSize="sm"
        class="aspect-square rounded-full p-0.5"
        @click="emit('close')"
      />
    </TCardHeader>
    <TCardBody>
      <div
        v-if="!!note"
        class="mb-4 rounded-lg border border-warning bg-warning/75 p-2 text-sm text-dark"
      >
        <span class="font-bold">Note: </span> {{ note }}
      </div>
      <TInput
        :label="editor.username.name"
        v-model="editor.username.value"
        :error="editor.username.error"
        :errorMessage="editor.username.errorMessage"
        innerClass="bg-light text-dark"
        @keyup.enter="saveUsername"
      />
    </TCardBody>
    <TCardFooter class="flex items-center justify-end gap-1">
      <TButton
        label="Save"
        class="rounded-full bg-primary bg-glossy px-3 py-1 text-light"
        @click="saveUsername"
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
import { inject, reactive, ref } from "vue";
import { InputField, notify, Helpers } from "@/scripts";

const $api = inject("$api");
const props = defineProps({
  user: Object,
  api: String,
  note: {
    type: String,
    default:
      "Changing the username will sign out the user from all of their sessions",
  },
});

const emit = defineEmits(["update:user", "close"]);

const loading = ref(false);
const loadingMessage = ref("");
const editor = reactive({
  username: new InputField(props.user.username)
    .setName("Username")
    .setRules("required|username"),
});

const saveUsername = () => {
  if (validate()) {
    loading.value = true;
    loadingMessage.value = "Updating username, please wait...";

    $api
      .patch(props.api, {
        username: editor.username.value,
      })
      .then((response) => {
        emit("update:user", response.data.data);
        notify({
          title: "Success!",
          type: "positive",
          text: response.data.message,
        });
      })
      .catch((error) => Helpers.onRequestError(error, editor))
      .finally(() => {
        loading.value = false;
      });
  }
};

const validate = () => {
  editor.username.validate();

  return !editor.username.error;
};
</script>
