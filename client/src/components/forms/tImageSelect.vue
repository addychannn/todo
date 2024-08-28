<template>
  <label
    :for="attr.id"
    class="relative flex select-none"
    @dragenter.prevent
    @dragover.prevent
    @dragleave.prevent
    @drop.prevent="onDrop"
    :class="[error != null && 'mb-5', circle && '!rounded-full']"
  >
    <input
      v-bind="attr"
      @change="onFileChange"
      class="hidden"
      ref="fileInput"
    />
    <div
      class="group relative min-h-[10rem] w-full overflow-hidden rounded-lg border border-dashed border-slate-400 bg-white shadow-md transition"
      :class="[
        disabled && 'cursor-not-allowed',
        !disabled && 'cursor-pointer hover:border-slate-700',
        error && '!border-negative',
        circle && '!rounded-full',
      ]"
    >
      <div v-if="!!imgSrc" class="absolute inset-0">
        <slot :src="imgSrc">
          <TImage :src="imgSrc" class="h-full w-full object-cover" />
        </slot>
      </div>
      <div
        class="absolute inset-0 flex-auto text-slate-400 transition"
        :class="[
          !!imgSrc &&
            !disabled &&
            'bg-dark/40 !text-slate-300 group-hover:!text-slate-50',
          !disabled && 'group-hover:text-slate-700',
          disabled && '',
        ]"
      >
        <slot
          name="overlay"
          :src="imgSrc"
          :disabled="disabled"
          :data="data"
          :clearFn="() => clearImage()"
        >
          <div
            class="relative flex h-full flex-col items-center justify-center overflow-hidden transition"
            :class="[!!imgSrc && 'opacity-20 group-hover:opacity-100']"
          >
            <TIcon name="image" size="xl" class="" />
            <div>Drag N' Drop image</div>
            <div>- OR -</div>
            <div>
              <button
                class="pointer-events-none rounded-md border px-2 py-1 uppercase leading-none"
              >
                Browse
              </button>
            </div>
            <span
              v-if="!!data?.name"
              class="absolute right-0 top-0 m-1 flex items-center"
            >
              <button
                class="relative overflow-hidden rounded-full p-1 leading-none"
                @click.prevent.stop="clearImage"
              >
                <FocusHelper color="bg-negative" />
                <TIcon name="close" />
              </button>
            </span>
          </div>
        </slot>
      </div>

      <div
        v-if="!!data?.name && !circle && !slots.overlay"
        class="absolute left-1 top-1 mr-10 max-w-[90%] flex-auto overflow-hidden text-ellipsis whitespace-nowrap rounded-lg bg-dark/75 p-1 px-2 text-sm leading-none text-slate-50"
      >
        {{ data.name }}
      </div>
    </div>
    <div
      v-if="error != null"
      class="absolute inset-x-0 bottom-0 translate-y-full px-2 pt-1 text-[11px] leading-none"
    >
      <transition
        enter-from-class="-translate-y-full opacity-0"
        leave-to-class="-translate-y-full opacity-0"
        enter-active-class="transition duration-300"
        leave-active-class="transition duration-300"
      >
        <div
          v-if="error === true"
          class="text-[11px] leading-none text-negative"
        >
          {{ errorMessage }}
        </div>
      </transition>
    </div>
  </label>
</template>

<script setup>
import { reactive, computed, ref, useSlots } from "vue";
import { Helpers } from "@/scripts";

const slots = useSlots();
const props = defineProps({
  modelValue: File,
  label: {
    type: String,
    default: null,
  },
  initialImageSrc: {
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
  disabled: {
    type: Boolean,
    default: false,
  },
  circle: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(["update:modelValue"]);

const fileInput = ref();
const data = computed({
  get: () => props.modelValue,
  set: (val) => emit("update:modelValue", val),
});

const attr = computed(() => ({
  id: Helpers.uniqid(),
  type: "file",
  accept: "image/*",
  disabled: props.disabled,
}));

const imgSrc = computed(() =>
  data.value ? URL.createObjectURL(data.value) : props.initialImageSrc ?? null
);

const onFileChange = (e) => {
  if (e.target.files.length > 0) {
    setFile(e.target.files[0]);
  }
};

const onDrop = (e) => {
  if (e.dataTransfer.files.length > 0) {
    setFile(e.dataTransfer.files[0]);
  }
};

const setFile = (file) => {
  if (isMimeValid(file, attr.value.accept)) {
    data.value = file;
  }
};

const isMimeValid = (file, accept) => {
  let MimeType = new RegExp(
    accept
      .replaceAll("*", "[^\\/,]+")
      .replaceAll(" ", "")
      .replaceAll(",", "|")
      .replaceAll("/", "\\/")
      .replaceAll(".", "\\.")
  );
  return MimeType.test(file.type);
};

const clearImage = () => {
  fileInput.value.type = "";
  fileInput.value.type = "file";
  data.value = null;
};
</script>
