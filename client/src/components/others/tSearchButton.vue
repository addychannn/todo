<template>
  <TButton
    class="w-64 rounded-full border border-slate-500 bg-light/25 px-1.5 py-0.5 text-start text-foreground shadow-inner"
    @click="emit('search')"
  >
    <div class="pointer-events-none flex flex-auto items-center px-2">
      <TIcon :name="icon" class="mr-1" />
      <div class="flex-auto leading-none">{{ label }}</div>
      <TButton
        icon="refresh"
        class="pointer-events-auto box-border rounded-full border-2 bg-foreground/10"
        darkFocus
        @click.stop="emit('reset')"
      />
    </div>
  </TButton>
</template>

<script setup>
const props = defineProps({
  label: {
    type: String,
    default: "Search",
  },
  icon: {
    type: String,
    default: "search",
  },
});

const emit = defineEmits(["search", "reset"]);
</script>
