<template>
  <TFormWizardTab v-model:fields="fields">
    <template #default="{ editor, next }">
      <TInput
        v-if="!!editor.username"
        :label="editor.username.name"
        v-model="editor.username.value"
        :error="editor.username.error"
        :errorMessage="editor.username.errorMessage"
        innerClass="bg-light text-dark"
        @keyup.enter="next()"
      />
      <TInput
        v-if="!!editor.email"
        :label="editor.email.name"
        v-model="editor.email.value"
        :error="editor.email.error"
        :errorMessage="editor.email.errorMessage"
        innerClass="bg-light text-dark"
        @keyup.enter="next()"
      />
      <TInput
        v-if="!!editor.password"
        :label="editor.password.name"
        v-model="editor.password.value"
        :error="editor.password.error"
        :errorMessage="editor.password.errorMessage"
        :type="pConfirm ? 'password' : 'text'"
        innerClass="bg-light text-dark"
        @keyup.enter="next()"
      >
        <template v-if="!pConfirm" #after>
          <TButton
            icon="loop"
            class="rounded-xl p-1"
            @click="generatePassword(editor.password)"
          />
        </template>
      </TInput>
      <TInput
        v-if="pConfirm && !!editor.password_confirmation"
        :label="editor.password_confirmation.name"
        v-model="editor.password_confirmation.value"
        :error="editor.password_confirmation.error"
        :errorMessage="editor.password_confirmation.errorMessage"
        type="password"
        innerClass="bg-light text-dark"
        @keyup.enter="next()"
      />
    </template>
  </TFormWizardTab>
</template>

<script setup>
import { computed, ref } from "vue";
import { InputField, Helpers } from "@/scripts";

const props = defineProps({
  pConfirm: {
    type: Boolean,
    default: false,
  },
  requireEmail: {
    type: Boolean,
    default: false,
  },
});

const fields = computed(() => {
  let emailRules = ["email"];
  if (props.requireEmail) {
    emailRules.unshift("required");
  }
  let _defaults = {
    username: new InputField()
      .setName("Username")
      .setRules("required|username"),
    email: new InputField().setName("Email").setRules(emailRules),
    password: new InputField()
      .setName("Password")
      .setRules("required|password"),
  };
  if (props.pConfirm) {
    Object.assign(_defaults, {
      password_confirmation: new InputField()
        .setName("Password Confirmation")
        .setRules("required|password"),
    });
  }
  return _defaults;
});

const generatePassword = async (pass) => {
  let iterations = 7;

  for (let i = 0; i < iterations; i++) {
    pass.value = Helpers.passwordGeneratorFn(8);
    await new Promise((resolve) => setTimeout(resolve, 50));
  }
};
</script>
