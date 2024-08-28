<template>
  <TButton
    icon="format_italic"
    class="box-border border p-1"
    iconSize="sm"
    :class="[
      editor?.isActive('italic')
        ? 'bg-dark/25 border-foreground/25'
        : 'border-transparent',
    ]"
    focusDisabled
    @click="editor?.chain().focus().toggleItalic().run()"
  />
</template>

<script setup>
const props = defineProps({
  editor: Object,
});
</script>
