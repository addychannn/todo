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
        v-if="!!note"
        class="mb-4 rounded-lg border border-warning bg-warning/75 p-2 text-sm text-dark"
      >
        <span class="font-semibold">Note: </span> {{ note }}
      </div>
      <TInput
        :label="editor.password.name"
        v-model="editor.password.value"
        :error="editor.password.error"
        :errorMessage="editor.password.errorMessage"
        innerClass="bg-light text-dark"
        :disabled="generating"
        @keyup.enter="savePassword"
      >
        <template #after>
          <TButton
            icon="loop"
            :disabled="generating"
            class="rounded-xl p-1"
            @click="generatePassword"
          />
        </template>
      </TInput>
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
import { inject, reactive, ref } from "vue";
import { InputField, notify, Helpers } from "@/scripts";

const $api = inject("$api");
const props = defineProps({
  user: Object,
  note: {
    type: String,
    default:
      "Changing the password will sign out the user from all of their sessions",
  },
});

const emit = defineEmits(["update:user", "close"]);

const loading = ref(false);
const loadingMessage = ref("");
const generating = ref(false);
const editor = reactive({
  password: new InputField().setName("Password").setRules("required|password"),
});

const savePassword = () => {
  if (validate()) {
    loading.value = true;
    loadingMessage.value = "Updating password, please wait...";

    $api
      .patch(`/users/password/${props.user.id}`, {
        password: editor.password.value,
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

const generatePassword = async () => {
  generating.value = true;

  let iterations = 7 * 2;

  for (let i = 0; i < iterations; i++) {
    editor.password.value = Helpers.passwordGeneratorFn(8);
    await new Promise((resolve) => setTimeout(resolve, 50));
  }
  generating.value = false;
};

const validate = () => {
  editor.password.validate();

  return !editor.password.error;
};
</script>
