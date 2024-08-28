<template>
  <TCard
    class="relative max-h-[95dvh] w-[95dvw] max-w-sm bg-background-accent text-foreground"
  >
    <TCardHeader class="bg-page-background">
      <TCardTitle> Email </TCardTitle>
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
        :label="editor.email.name"
        v-model="editor.email.value"
        :error="editor.email.error"
        :errorMessage="editor.email.errorMessage"
        innerClass="bg-light text-dark"
        @keyup.enter="saveEmail"
      />
    </TCardBody>
    <TCardFooter class="flex items-center justify-end gap-1">
      <TButton
        label="Save"
        class="rounded-full bg-primary bg-glossy px-3 py-1 text-light"
        @click="saveEmail"
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
import { transitions, notify, InputField } from "@/scripts";

const $api = inject("$api");
const props = defineProps({
  user: Object,
  api: String,
  note: {
    type: String,
    default:
      "The user's account will become invalid if the email is changed, and they will have to go through the email verification process.",
  },
});

const emit = defineEmits(["update:user", "close"]);

const loading = ref(false);
const loadingMessage = ref("");

const editor = reactive({
  email: new InputField(props.user.email)
    .setName("Email")
    .setRules("required|email"),
});

const saveEmail = () => {
  if (validate()) {
    loading.value = true;
    loadingMessage.value = "Updating email, please wait...";

    $api
      .patch(props.api, {
        email: editor.email.value,
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
  editor.email.validate();
  return !editor.email.error;
};
</script>
