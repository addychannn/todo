<template>
  <TButton
    icon="strikethrough_s"
    class="box-border border p-1"
    iconSize="sm"
    :class="[
      editor?.isActive('strike')
        ? 'bg-dark/25 border-foreground/25'
        : 'border-transparent',
    ]"
    focusDisabled
    @click="editor?.chain().focus().toggleStrike().run()"
  />
</template>

<script setup>
const props = defineProps({
  editor: Object,
});
</script>
