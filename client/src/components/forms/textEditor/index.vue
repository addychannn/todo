<template>
  <div
    class="relative rounded-md border border-foreground/25 shadow-md shadow-foreground/25"
    :class="[error != null && 'mb-5']"
  >
    <template v-if="!!editor">
      <div class="flex items-center gap-0.5 border-b border-inherit px-2 py-1">
        <div
          v-if="!!label"
          class="border-r border-inherit px-2"
          :class="[error && 'animate-shake text-negative']"
        >
          <span class="font-semibold leading-tight">{{ label }}</span>
        </div>
        <component
          v-for="component in actions"
          :key="component"
          :is="component"
          :editor="editor"
          class="rounded-sm"
        />
      </div>
      <div class="px-3 py-1" :style="{ minHeight: `${height + 8}px` }">
        <EditorContent :editor="editor" :id="id" />
      </div>
    </template>
    <div
      v-if="error != null && errorMessage != null"
      class="absolute inset-x-0 bottom-0 translate-y-full px-2 pt-1 text-[11px] leading-none"
    >
      <transition
        enterFromClass="opacity-0 -translate-y-full"
        leaveToClass="opacity-0 -translate-y-full"
        enterActiveClass="transition duration-300"
        leaveActiveClass="transition duration-300"
      >
        <div
          v-if="error"
          class="absolute left-2 right-0 top-0.5 mt-0.5 text-[11px] font-semibold leading-none text-light"
        >
          <slot name="errorMessage" :error="error" :errorMessage="errorMessage">
            <span
              class="rounded-full border border-negative bg-negative/75 px-1 leading-tight"
            >
              {{ errorMessage }}
            </span>
          </slot>
        </div>
      </transition>
    </div>
  </div>
</template>

<script setup>
import {
  computed,
  defineAsyncComponent,
  onBeforeUnmount,
  onMounted,
  ref,
  watch,
} from "vue";
import { Editor, EditorContent } from "@tiptap/vue-3";
import { Blockquote } from "@tiptap/extension-blockquote";
import { Bold } from "@tiptap/extension-bold";
import { BulletList } from "@tiptap/extension-bullet-list";
import { Code } from "@tiptap/extension-code";
import { CodeBlock } from "@tiptap/extension-code-block";
import { Document } from "@tiptap/extension-document";
import { Dropcursor } from "@tiptap/extension-dropcursor";
import { Gapcursor } from "@tiptap/extension-gapcursor";
import { HardBreak } from "@tiptap/extension-hard-break";
import { Heading } from "@tiptap/extension-heading";
import { History } from "@tiptap/extension-history";
import { HorizontalRule } from "@tiptap/extension-horizontal-rule";
import { Italic } from "@tiptap/extension-italic";
import { ListItem } from "@tiptap/extension-list-item";
import { OrderedList } from "@tiptap/extension-ordered-list";
import { Paragraph } from "@tiptap/extension-paragraph";
import { Strike } from "@tiptap/extension-strike";
import { Text } from "@tiptap/extension-text";
import { Helpers } from "@/scripts";
const props = defineProps({
  modelValue: String,
  rows: {
    type: Number,
    default: 3,
  },
  label: {
    type: String,
    default: null,
  },
  error: {
    type: Boolean,
    default: null,
  },
  errorMessage: {
    type: String,
    default: null,
  },
});

const emit = defineEmits(["update:modelValue", "focus", "blur", "isEmpty"]);

const actions = {
  bold: defineAsyncComponent(() => import("./actions/bold.vue")),
  italic: defineAsyncComponent(() => import("./actions/italic.vue")),
  strike: defineAsyncComponent(() => import("./actions/strikeThrough.vue")),
};

const id = ref(Helpers.uniqid());
const hasFocus = ref(false);
const editor = ref(null);

// (initial hieght) * (No. of rows) + (top and bottom padding) + (top and bottom border)
const height = computed(
  () => 25.59 * (props.rows <= 0 ? 1 : props.rows) + 8 + 2
);

const isEmpty = computed(
  () => editor.value?.state.doc.textContent.trim().length <= 0
);

const onEditorUpdate = () => {
  let content = editor.value.getHTML();
  emit("update:modelValue", isEmpty.value ? null : content);
};

watch(
  () => props.modelValue,
  (val) => {
    let isSame = editor.value?.getHTML() === val;
    if (!isSame) {
      editor.value.commands.setContent(val, false);
    }
  }
);

watch(hasFocus, (val) => {
  emit(val ? "focus" : "blur");
});

watch(isEmpty, (val) => {
  emit("isEmpty", val);
});

onMounted(() => {
  editor.value = new Editor({
    extensions: [
      Blockquote,
      Bold,
      BulletList,
      Code,
      CodeBlock,
      Document,
      Dropcursor,
      Gapcursor,
      Heading,
      History,
      HorizontalRule,
      Italic,
      ListItem,
      OrderedList,
      Paragraph,
      Strike,
      Text,
      HardBreak.extend({
        addKeyboardShortcuts() {
          return {
            Enter: () => editor.value.commands.setHardBreak(),
          };
        },
      }),
    ],
    editorProps: {
      attributes: {
        class: "prose outline-none",
        style: `min-height: ${height.value}px`,
      },
    },
    content: props.modelValue,
    onUpdate: () => onEditorUpdate(),
    onFocus: () => {
      hasFocus.value = true;
    },
    onBlur: () => {
      hasFocus.value = false;
    },
  });
});

onBeforeUnmount(() => {
  editor.value.destroy();
});
</script>
