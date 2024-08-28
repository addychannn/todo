<template>
  <TCard
    class="relative max-h-[95dvh] w-[95dvw] max-w-sm bg-background-accent text-foreground"
  >
    <TCardHeader>
      <TCardTitle>Permission Editor</TCardTitle>
      <TButton
        icon="close"
        iconSize="sm"
        class="aspect-square rounded-full p-0.5"
        @click="emit('close')"
      />
    </TCardHeader>
    <TCardBody>
      <TInput
        v-model="editor.name.value"
        :label="editor.name.name"
        :error="editor.name.error"
        :errorMessage="editor.name.errorMessage"
        innerClass="bg-light text-dark"
        @keyup.enter="savePermission"
      />
      <TTextArea
        v-model="editor.description.value"
        :label="editor.description.name"
        :error="editor.description.error"
        :errorMessage="editor.description.errorMessage"
        innerClass="bg-light text-dark"
      />
    </TCardBody>
    <TCardFooter class="flex items-center justify-end gap-1">
      <TButton
        label="Save"
        class="rounded-full bg-primary bg-glossy px-3 py-1 text-light"
        @click="savePermission"
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
import { computed, inject, ref, reactive } from "vue";
import { InputField, notify } from "@/scripts";

const $api = inject("$api");

const props = defineProps({
  modelValue: Object,
});

const emit = defineEmits(["update:modelValue", "close"]);

const loading = ref(false);
const loadingMessage = ref("");

const editor = reactive({
  name: new InputField(props.modelValue?.name)
    .setName("Name")
    .setRules("required"),
  description: new InputField(props.modelValue?.description)
    .setName("Description")
    .setRules(""),
});

const isEdit = computed(() => !!props.modelValue?.id);

const savePermission = () => {
  if (validate()) {
    loading.value = true;
    loadingMessage.value = "Saving permission, please wait...";
    let method = isEdit.value ? "patch" : "post";
    let uri = `/permissions/${isEdit.value ? props.modelValue.id : ""}`;
    $api[method](uri, {
      name: editor.name.value,
      description: editor.description.value,
    })
      .then((response) => {
        emit("update:modelValue", response.data.data);
        notify({
          type: "positive",
          title: "Success!",
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
  editor.name.validate();
  editor.description.validate();

  return !(editor.name.error || editor.description.error);
};
</script>
