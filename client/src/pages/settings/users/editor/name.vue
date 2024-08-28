<template>
  <TCard
    class="relative max-h-[95dvh] w-[95dvw] max-w-sm bg-background-accent text-foreground"
  >
    <TCardHeader class="bg-page-background">
      <TCardTitle> Name </TCardTitle>
      <TButton
        icon="close"
        iconSize="sm"
        class="aspect-square rounded-full p-0.5"
        @click="emit('close')"
      />
    </TCardHeader>
    <TCardBody>
      <TInput
        :label="editor.first_name.name"
        v-model="editor.first_name.value"
        :error="editor.first_name.error"
        :errorMessage="editor.first_name.errorMessage"
        innerClass="bg-light text-dark"
        @keyup.enter="saveName"
      />
      <TInput
        :label="editor.middle_name.name"
        v-model="editor.middle_name.value"
        :error="editor.middle_name.error"
        :errorMessage="editor.middle_name.errorMessage"
        innerClass="bg-light text-dark"
        @keyup.enter="saveName"
      />
      <TInput
        :label="editor.last_name.name"
        v-model="editor.last_name.value"
        :error="editor.last_name.error"
        :errorMessage="editor.last_name.errorMessage"
        innerClass="bg-light text-dark"
        @keyup.enter="saveName"
      />
      <TInput
        :label="`${editor.suffix.name}`"
        hint="(Jr., Sr., II, III, etc...)"
        v-model="editor.suffix.value"
        :error="editor.suffix.error"
        :errorMessage="editor.suffix.errorMessage"
        innerClass="bg-light text-dark"
        @keyup.enter="saveName"
      />
      <TInput
        :label="editor.nickname.name"
        v-model="editor.nickname.value"
        :error="editor.nickname.error"
        :errorMessage="editor.nickname.errorMessage"
        innerClass="bg-light text-dark"
        @keyup.enter="saveName"
      />
    </TCardBody>

    <TCardFooter class="flex items-center justify-end gap-1">
      <TButton
        label="Save"
        class="rounded-full bg-primary bg-glossy px-3 py-1 text-light"
        @click="saveName"
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
import { InputField, notify } from "@/scripts";

const $api = inject("$api");

const props = defineProps({
  user: Object,
  api: String,
});

const emit = defineEmits(["update:user", "close"]);

const loading = ref(false);
const loadingMessage = ref("");

const editor = reactive({
  first_name: new InputField(props.user.profile?.first_name)
    .setName("First Name")
    .setRules("required"),
  middle_name: new InputField(props.user.profile?.middle_name)
    .setName("Middle Name/Initial")
    .setRules(),
  last_name: new InputField(props.user.profile?.last_name)
    .setName("LastName")
    .setRules("required"),
  suffix: new InputField(props.user.profile?.suffix).setName("Suffix"),
  nickname: new InputField(props.user.profile?.nickname).setName("Nickname"),
});

const saveName = () => {
  if (validate()) {
    loading.value = true;
    loadingMessage.value = "Updating profile name, please wait...";

    $api
      .patch(props.api, {
        first_name: editor.first_name.value,
        middle_name: editor.middle_name.value,
        last_name: editor.last_name.value,
        suffix: editor.suffix.value,
        nickname: editor.nickname.value,
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
  editor.first_name.validate();
  editor.middle_name.validate();
  editor.last_name.validate();
  editor.suffix.validate();
  editor.nickname.validate();
  return !(
    editor.first_name.error ||
    editor.middle_name.error ||
    editor.last_name.error ||
    editor.suffix.error ||
    editor.nickname.error
  );
};
</script>
