<template>
  <div class="flex h-full items-center">
    <div
      class="relative grid w-full rounded-lg border border-foreground/25 bg-page-background p-2 shadow-md shadow-foreground/10"
    >
      <div class="flex items-center gap-1 text-lg font-semibold">
        <TIcon name="warning" class="text-warning" /> Delete Address?
      </div>
      <div class="flex-auto">
        <p>
          You are going to remove an address; this action cannot be reversed.
          Would you wish to continue?
        </p>
        <div>
          <div class="font-bold">Address:</div>
          <div class="px-2">
            {{ address.full }}
          </div>
        </div>
      </div>
      <div class="flex items-center justify-end gap-2">
        <TButton
          label="Yes"
          class="rounded-md border-primary bg-primary bg-glossy px-3 py-1 text-light"
          @click="deleteAddress"
        />
        <TButton
          label="Cancel"
          class="rounded-md px-3 py-1"
          :disabled="_loading"
          @click="emit('cancel')"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, inject } from "vue";
import { notify } from "@/scripts";
import { useVModel } from "@vueuse/core";

const $api = inject("$api");

const props = defineProps({
  address: Object,
  api: String,
  loading: {
    type: Boolean,
    default: false,
  },
  loadingMessage: {
    type: String,
    default: "",
  },
});

const emit = defineEmits([
  "update:user",
  "update:loading",
  "update:loadingMessage",
  "cancel",
]);

const _loading = useVModel(props, "loading", emit);
const _loadingMessage = useVModel(props, "loadingMessage", emit);

const deleteAddress = () => {
  _loading.value = true;
  _loadingMessage.value = "Removing address, please wait...";

  $api
    .delete(`${props.api}/${props.address.id}`)
    .then((response) => {
      emit("update:user", response.data.data);
      notify({
        title: "Success!",
        type: "positive",
        text: response.data.message,
      });
    })
    .finally(() => {
      _loading.value = false;
    });
};
</script>
