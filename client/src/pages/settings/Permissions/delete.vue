<template>
  <TCard class="relative max-h-[95dvh] w-[95dvw] max-w-sm">
    <TCardHeader class="dark:!border-background/25">
      <TCardTitle class="flex items-center">
        <TIcon name="warning" class="mr-1 text-warning" /> Delete Permission
      </TCardTitle>
      <TButton
        icon="close"
        iconSize="sm"
        class="aspect-square rounded-full p-0.5"
        @click="emit('close')"
      />
    </TCardHeader>
    <TCardBody>
      <div>
        Are you sure you want to delete this permission? This action cannot be
        undone.
      </div>
      <div class="px-2 py-1 text-sm">
        <span class="font-semibold">Permission: </span>
        {{ Helpers.dashToHuman(modelValue.name) }}
      </div>
      <div class="pointer-events-none select-none">
        Type "
        <span class="font-bold italic">{{ response }}</span>
        " to proceed.
      </div>
    </TCardBody>
    <TCardFooter class="flex items-center gap-2">
      <label
        class="flex-auto rounded-lg border border-dark/25 bg-light px-2 py-0.5 text-dark outline outline-1 outline-transparent transition-all focus-within:outline-primary"
      >
        <input
          v-model="answer"
          placeholder="Your answer"
          type="text"
          class="w-full bg-inherit outline-none"
        />
      </label>
      <TButton
        label="Yes"
        class="rounded-full bg-negative bg-glossy px-3 py-1 text-light"
        @click="deletePermission"
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
import { ref, inject } from "vue";
import { Helpers, notify } from "@/scripts";

const $api = inject("$api");
const props = defineProps({
  modelValue: Object,
});

const emit = defineEmits(["delete", "close"]);
const response = "CONFIRM";
const answer = ref();
const loading = ref(false);
const loadingMessage = ref("");

const deletePermission = () => {
  if (answer.value === response) {
    loading.value = true;
    loadingMessage.value = "Deleting Permission, please wait...";
    $api
      .delete(`/permissions/${props.modelValue.id}`)
      .then((response) => {
        emit("delete", props.modelValue);
        notify({
          type: "positive",
          title: "Success!",
          text: response.data.message,
        });
      })
      .finally(() => {
        loading.value = false;
      });
  } else {
    emit("close");
  }
};
</script>
