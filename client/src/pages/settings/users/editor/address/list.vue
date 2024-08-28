<template>
  <div class="grid flex-auto gap-2">
    <div class="flex items-center justify-end">
      <TButton
        label="Add Address"
        icon="add_circle_outline"
        class="rounded-full border border-foreground bg-light px-2 py-1 text-dark"
        @click="emit('create')"
      />
    </div>
    <div
      v-if="addresses.length <= 0"
      class="text-center font-semibold italic text-gray-400"
    >
      No Address
    </div>
    <div
      v-for="address in addresses"
      :key="address.id"
      class="flex select-none items-center gap-1 rounded-lg border border-foreground/25 bg-page-background px-2 py-0.5 font-semibold transition-transform hover:bg-primary/25"
    >
      <div
        class="r flex flex-auto items-center gap-1"
        :class="!address.isMain && 'cursor-pointer'"
        @click="!address.isMain && emit('change', address)"
      >
        <TIcon
          :name="
            address.isMain ? 'radio_button_checked' : 'radio_button_unchecked'
          "
          size="sm"
        />
        <div class="line-clamp-1 flex-auto leading-tight">
          {{ address.full }}
        </div>
      </div>
      <div class="flex items-center gap-1">
        <TButton
          icon="edit"
          iconSize="sm"
          class="aspect-square w-[1.625rem] rounded-full p-1 hover:text-primary"
          @click="emit('edit', address)"
        />
        <TButton
          icon="delete"
          iconSize="sm"
          :disabled="address.isMain"
          class="aspect-square w-[1.625rem] rounded-full p-1 hover:text-negative"
          @click="emit('delete', address)"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
const props = defineProps({
  addresses: Object,
});

const emit = defineEmits(["edit", "delete", "create", "change"]);
</script>
