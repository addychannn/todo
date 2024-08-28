<template>
  <TCard
    class="relative max-h-[95dvh] w-[95dvw] max-w-sm bg-background-accent text-foreground"
  >
    <TCardHeader class="bg-page-background">
      <TCardTitle> Change account status? </TCardTitle>
      <TButton
        icon="close"
        iconSize="sm"
        class="aspect-square rounded-full p-0.5"
        @click="emit('close')"
      />
    </TCardHeader>
    <TCardBody class="text-sm">
      You are about to {{ user.active ? "deactivate" : "activate" }} this
      account. Are you certain you want to move forward?
    </TCardBody>
    <TCardFooter class="flex items-center justify-end gap-1">
      <TButton
        label="Yes"
        class="rounded-full px-3 py-1"
        @click="toggleAccount"
      />
      <TButton
        label="Cancel"
        class="rounded-full bg-primary bg-glossy px-3 py-1 text-light"
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
});

const emit = defineEmits(["update:user", "close"]);

const loading = ref(false);
const loadingMessage = ref("");

const toggleAccount = () => {
  loading.value = true;
  loadingMessage.value = `${
    props.user.active ? "Deactivating" : "Activating"
  } account, please wait...`;

  $api
    .patch(`/users/disable/${props.user.id}`)
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
};
</script>
