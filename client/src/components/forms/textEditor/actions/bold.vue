<template>
  <TButton
    icon="format_bold"
    class="box-border border p-1"
    iconSize="sm"
    :class="[
      editor?.isActive('bold')
        ? 'bg-dark/25 border-foreground/25'
        : 'border-transparent',
    ]"
    focusDisabled
    @click="editor?.chain().focus().toggleBold().run()"
  />
</template>

<script setup>
const props = defineProps({
  editor: Object,
});
</script>
