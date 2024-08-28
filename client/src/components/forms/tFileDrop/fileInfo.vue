<template>
  <div
    class="group/file relative flex w-full flex-auto select-none items-center overflow-hidden bg-background-accent py-1 leading-none transition"
    :class="[readonly && 'select-none text-gray-400']"
  >
    <div class="flex justify-center px-2 leading-none">
      <TFileIcon :name="getFileExt(file)" class="w-8" />
    </div>
    <div class="grid flex-auto gap-1 leading-none">
      <div
        class="overflow-x-hidden whitespace-nowrap border-b border-foreground/50 text-xs font-semibold leading-tight"
      >
        <input
          v-model="_rename"
          class="w-full overflow-hidden bg-inherit px-1 outline-none"
          maxlength="255"
          :disabled="readonly"
        />
      </div>
      <div class="flex items-center">
        <div class="flex-auto">
          <div class="text-xs leading-none">
            <span class="font-semibold">Size: </span>
            {{ Helpers.formatFileSize(file.size) }}
          </div>
          <div class="line-clamp-1 overflow-x-hidden text-xs leading-none">
            <span class="font-semibold">Type: </span>
            {{ getMimeType(file) }}
          </div>
        </div>
        <div class="flex items-center justify-center px-2">
          <TProgressCircle
            :value="progress"
            class="!w-7 !bg-background-accent from-positive via-positive to-positive text-xs transition before:w-[70%]"
            :class="[!progress && 'invisible']"
          />
        </div>
      </div>
    </div>
    <div
      v-if="!readonly"
      class="flex items-stretch justify-stretch self-stretch overflow-x-hidden whitespace-nowrap rounded-l-lg border-y border-l border-foreground/50 bg-background transition group-hover/file:translate-x-0 md:absolute md:inset-y-0 md:right-0 md:translate-x-full"
    >
      <slot name="actions" :file="file" :progress="progress"></slot>
      <TButton
        icon="delete"
        class="px-1 text-negative/75"
        tabindex="-1"
        @click="emit('remove')"
      />
    </div>
  </div>
</template>

<script setup>
import { Helpers, MimeTypes } from "@/scripts";
import { computed } from "vue";

const props = defineProps({
  file: File,
  rename: {
    type: String,
    default: null,
  },
  readonly: {
    type: Boolean,
    default: false,
  },
  progress: {
    type: Number,
    default: 0,
    validator: (val) => val >= 0 && val <= 100,
  },
});

const emit = defineEmits(["remove", "update:rename"]);

const _rename = computed({
  get: () => props.rename,
  set: (val) => emit("update:rename", Helpers.filenameValidator(val)),
});

const getFileExt = (file) => {
  return MimeTypes.extractExtension(file.name);
};

const getMimeType = (file) => {
  if (!file.type) {
    let mime = MimeTypes.lookup(file.name);
    return !!mime ? mime : "UNKNOWN";
  } else {
    return file.type;
  }
};
</script>
