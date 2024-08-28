<template>
  <div class="grid gap-1">
    <slot
      :editor="editor"
      :next="() => emit('next')"
      :prev="() => emit('prev')"
    ></slot>
  </div>
</template>

<script setup>
import { useVModel } from "@vueuse/core";
import { inject, onMounted, ref, watch } from "vue";
import { InputField } from "@/scripts";

const $api = inject("$api");

const props = defineProps({
  inputFields: {
    type: Object,
    default: () => ({}),
  },
  fields: {
    type: Object,
    default: () => ({}),
  },
});

const emit = defineEmits([
  "update:inputFields",
  "update:fields",
  "next",
  "prev",
  "fields",
]);

const roles = ref([]);

const editor = useVModel(props, "inputFields", emit);
const _fields = useVModel(props, "fields", emit);

const fieldExists = (name) => Object.keys(props.inputFields).includes(name);

const assignFields = () => {
  Object.keys(_fields.value).forEach((field) => {
    if (!fieldExists(field)) {
      Object.assign(editor.value, { [field]: _fields.value[field] });
    } else {
      _fields.value[field].setValue(editor.value[field]?.value ?? null);
    }
  });
};

onMounted(() => {
  assignFields();
  emit("fields", Object.keys(_fields.value));
});
</script>
