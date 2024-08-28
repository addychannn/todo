<template>
  <div
    class="group relative flex flex-col gap-1 border-4 border-dashed border-gray-400/75 pb-5 pt-10 transition-colors hover:border-foreground/75"
    :class="[
      (disabled || uploading) && 'pointer-events-none',
      error != null && 'mb-5',
      error && 'border-negative/50 hover:border-negative ',
    ]"
    @dragenter.prevent
    @dragover.prevent
    @dragleave.prevent
    @drop.prevent="onDrop"
  >
    <transition
      enterActiveClass="transition-all"
      leaveActiveClass="transition-all"
      enterFromClass="opacity-0"
      leaveToClass="opacity-0"
    >
      <div
        class="pointer-events-none absolute flex select-none items-center justify-center text-sm font-bold uppercase transition-all md:text-base"
        :class="[
          files?.length <= 0 && 'inset-0 flex-col',
          files?.length > 0 &&
            'inset-x-0 top-0  rounded-lg border-b border-slate-500/50 px-2 py-2',
        ]"
      >
        <template v-if="!uploading">
          <div class="italic leading-none transition-all">
            Drag N' Drop File{{ multiple ? "s" : "" }} here
          </div>
          <div
            class="transition-all"
            :class="[files?.length > 0 && 'flex-auto text-center']"
          >
            OR
          </div>
          <TButton
            label="Browse"
            class="rounded-full border border-slate-500/50 bg-foreground bg-glossy px-3 py-1 uppercase text-background"
            :class="[!disabled && !uploading && 'pointer-events-auto']"
            @click="openFileDialog"
          />
        </template>
        <template v-else>
          <div class="flex w-full items-center">
            <div class="flex-auto">Uploading files(s), please wait...</div>
            <div>
              <TSpinnerCircles class="h-4 w-4" />
            </div>
          </div>
        </template>
      </div>
    </transition>
    <transition-group
      tag="div"
      name="files"
      class="relative flex flex-col gap-1"
      :class="[!!files?.length && ' bg-page-background p-1']"
    >
      <div
        v-for="item in files"
        :key="item.id"
        v-show="item.progress <= 100"
        class="w-full leading-none"
      >
        <slot
          :file="item.file"
          :readonly="disabled"
          :removeFn="() => removeFile(item.id)"
          :renameFn="(val) => onRename(val, item.id)"
          :progress="item.progress"
        >
          <FileInfo
            :rename="item.rename"
            @update:rename="(val) => onRename(val, item.id)"
            :file="item.file"
            @remove="removeFile(item.id)"
            :readonly="disabled || uploading"
            :progress="item.progress"
          >
            <template #actions="{ file, progress }">
              <slot name="actions" :file="file" :progress="progress"></slot>
            </template>
          </FileInfo>
        </slot>
      </div>
    </transition-group>
    <transition
      enterActiveClass="transition-all"
      leaveActiveClass="transition-all"
      enterFromClass="opacity-0"
      leaveToClass="opacity-0"
    >
      <div
        v-if="files?.length > 0"
        class="absolute inset-x-0 bottom-0 flex h-5 items-center"
      >
        <div class="flex w-full items-center gap-1 px-2 text-xs text-gray-400">
          <div class="flex-auto">
            {{ `${files.length} file${files.length > 1 ? "s" : ""} selected` }}
          </div>
          <div>{{ Helpers.formatFileSize(totalSize) }}</div>
        </div>
      </div>
    </transition>
    <div
      v-if="(error != null && errorMessage != null) || hint != null"
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
        <div
          v-else
          class="absolute inset-x-2 top-1 text-[11px] leading-none text-gray-400"
        >
          <slot name="hint" :hint="hint">
            {{ hint }}
          </slot>
        </div>
      </transition>
    </div>
  </div>
</template>

<script>
const ACCEPT_CHECK_REG = /^\.[a-zA-Z]{3,4}(,\.[a-zA-Z]{3,4})*$/;
</script>

<script setup>
import { Helpers, MimeTypes } from "@/scripts";
import { computed } from "vue";
import FileInfo from "./tFileDrop/fileInfo.vue";

const fileStatuses = ["pending", "uploading", "uploaded", "failed"];

const props = defineProps({
  modelValue: Object,
  disabled: {
    type: Boolean,
    default: false,
  },

  /**
   * The accepted file types for the file selection module.
   * (Must only be file extentions separated by commas)
   */
  accept: {
    type: String,
    default: null,
    validator: (val) => {
      if (
        !ACCEPT_CHECK_REG.test(val.replaceAll(" ", "")) &&
        import.meta.env.DEV
      ) {
        console.warn(
          '[Vue Warn]: Invalid prop: "Accept" prop must be a collection of file extensions delimited by a ","'
        );
      }
      return true;
    },
  },
  multiple: {
    type: Boolean,
    default: false,
  },
  allowDuplicates: {
    type: Boolean,
    default: false,
  },
  prepend: {
    type: Boolean,
    default: false,
  },
  alwaysReplace: {
    type: Boolean,
    default: false,
  },
  uploading: {
    type: Boolean,
    default: false,
  },
  hint: {
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

const emit = defineEmits(["update:modelValue"]);

const files = computed({
  get: () => props.modelValue,
  set: (val) => emit("update:modelValue", val),
});

const totalSize = computed(() => {
  let size = 0;
  files.value.forEach((file) => {
    size += file.file.size;
  });
  return size;
});

const openFileDialog = () => {
  let input = document.createElement("input");
  input.type = "file";
  input.multiple = props.multiple;
  input.accept = props.accept ?? "*";
  input.onchange = (e) => {
    addFiles([...e.target.files]);
  };
  input.click();
};

const onDrop = (e) => {
  addFiles([...e.dataTransfer.files]);
};

const addFiles = (new_files) => {
  if (!props.disabled && !props.uploading) {
    let _files = props.alwaysReplace ? [] : props.modelValue;
    let tmp = selectValidFiles(new_files);
    if (props.multiple) {
      props.prepend ? _files.unshift(...tmp.reverse()) : _files.push(...tmp);
    } else {
      _files = tmp[0] ? [tmp[0]] : [];
    }
    emit("update:modelValue", _files);
  }
};

const removeFile = (id) => {
  if (props.multiple) {
    let _files = [...props.modelValue];
    _files = _files.filter((f) => f.id !== id);
    emit("update:modelValue", _files);
  } else {
    emit("update:modelValue", []);
  }
};

const selectValidFiles = (files) => {
  let out = [];
  for (let file of files) {
    if (checkMime(file, props.accept) && !fileExists(file)) {
      out.push(constructFileObject(file));
    }
  }
  return out;
};

const fileExists = (file) => {
  return !!files.value.find((item) => item.file.name == file.name);
};

const onRename = (newName, id) => {
  let tmp = files.value.map((item) => {
    if (item.id == id) item.rename = newName;
    return item;
  });
  emit("update:modelValue", tmp);
};

const constructFileObject = (file) => {
  return {
    id: Helpers.uniqid(),
    progress: 0,
    rename: file.name,
    file: file,
  };
};

const checkMime = (file, mime) => {
  if (!!props.accept) {
    let exts = props.accept.replaceAll(" ", "").split(",");
    exts = exts.filter((ext) => ACCEPT_CHECK_REG.test(ext));
    let ext = MimeTypes.extractExtension(file.name);
    return !!exts.find((item) => item == `.${ext}`);
  }
  return true;
};
</script>

<style scoped>
.files-move,
.files-enter-active,
.files-leave-active {
  @apply transition-all duration-500;
}

.files-enter-from,
.files-leave-to {
  @apply translate-x-20 opacity-0;
}

.files-leave-active {
  @apply absolute;
}
</style>
