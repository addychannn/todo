<template>
  <label
    class="relative flex h-[2.475rem] min-w-[7rem] cursor-pointer select-none items-center rounded-lg border border-foreground/25"
    :class="[error != null && 'mb-5']"
  >
    <div class="relative flex-auto self-stretch overflow-hidden rounded-l-lg">
      <div class="absolute inset-0 bg-checkered-xs opacity-20" />
      <div
        class="absolute inset-0 flex items-center bg-gradient-to-r from-transparent"
        :class="
          isBright(Helpers.hex2rgba(selectedColor)) ? 'text-dark' : 'text-light'
        "
        :style="`background-color: ${selectedColor}`"
      >
        <div class="px-3 pt-3">
          {{ selectedColor }}
        </div>
      </div>
    </div>
    <TPopover
      icon="palette"
      btnClass="py-1 px-3 bg-glossy bg-primary text-light rounded-r-lg"
      class="h-full items-stretch"
      placement="right"
    >
      <template #default="{ close }">
        <div class="p-1">
          <div class="w-64 bg-background text-foreground">
            <ColorPicker
              :color="currentColor"
              :visible-formats="['hex']"
              default-format="hex"
              @colorChange="onColorChange"
            >
              <template #copy-button><span></span></template>
            </ColorPicker>
            <div class="px-2">
              <TInput
                v-model="currentColor"
                :innerStyle="{ backgroundColor: colorData?.cssColor }"
                :innerClass="[
                  isBright(colorData?.colors.rgb) ? 'text-dark' : 'text-light',
                ]"
              >
                <template #before>
                  <TButton
                    icon="refresh"
                    iconSize="sm"
                    class="rounded-lg p-2"
                    @click="currentColor = randomColor()"
                  />
                </template>
                <template #after>
                  <TButton
                    icon="copy"
                    iconSize="sm"
                    class="rounded-lg p-2"
                    @click="onCopy"
                  />
                </template>
              </TInput>
            </div>
            <div class="flex items-center justify-end px-2 py-1">
              <TButton
                label="Ok"
                class="rounded-lg bg-primary bg-glossy px-3 py-1 text-light"
                @click="selectColor(), close()"
              />
            </div>
          </div>
        </div>
      </template>
    </TPopover>
    <div
      class="absolute left-1 top-0.5 origin-top-left text-xs font-semibold transition-all"
      :class="[
        isBright(Helpers.hex2rgba(selectedColor)) ? 'text-dark' : 'text-light',
        !selectedColor && '!top-2 scale-150',
        error && 'animate-shake !text-negative',
      ]"
    >
      Color
    </div>
    <div
      v-if="error != null && errorMessage != null"
      class="absolute inset-x-0 bottom-0 translate-y-full px-2 pt-1 text-[11px] leading-none"
    >
      <transition
        enter-from-class="-translate-y-full opacity-0"
        leave-to-class="-translate-y-full opacity-0"
        enter-active-class="transition duration-300"
        leave-active-class="transition duration-300"
      >
        <div
          v-if="error"
          class="mt-0.5 text-[11px] font-semibold leading-none text-light"
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
  </label>
</template>

<script setup>
import { computed, onMounted, ref } from "vue";
import { ColorPicker } from "vue-accessible-color-picker";
import { Helpers, notify, transitions } from "@/scripts";
const props = defineProps({
  modelValue: String,
  error: {
    type: Boolean,
    default: null,
  },
  errorMessage: {
    type: String,
    default: null,
  },
  initialColor: {
    type: String,
    default: null,
  },
  randomizeOnLoad: {
    type: false,
    default: null,
  },
});

const emit = defineEmits(["update:modelValue"]);

const currentColor = ref("");
const selectedColor = ref();
const colorData = ref();

const onColorChange = (color) => {
  currentColor.value = color.cssColor;
  colorData.value = color;
};
const randomColor = () => {
  let rgb = Math.floor(Math.random() * 16777215).toString(16); // multiply by 255 to include alpha value
  return `#${rgb}`;
};
const isBright = (rgba) => {
  if (!rgba) return false;
  const rgbBrightness = Math.round(
    Helpers.brightness(rgba.r * 255, rgba.g * 255, rgba.b * 255)
  );
  return rgba.a >= 0.5 ? rgbBrightness > 125 : false;
};
const onCopy = () => {
  navigator.clipboard.writeText(currentColor.value);
  notify({
    title: "Copied to clipboard!",
    type: "positive",
    text: `Color: ${currentColor.value}`,
  });
};
const selectColor = () => {
  selectedColor.value = colorData.value.cssColor;
  emit("update:modelValue", colorData.value.cssColor);
};

onMounted(() => {
  let _initColor =
    props.modelValue ??
    (props.randomizeOnLoad ? randomColor() : props.initialColor ?? "#FFFFFF");
  currentColor.value = _initColor;
  colorData.value = {
    cssColor: _initColor,
    colors: {
      hex: _initColor,
      rgb: Helpers.hex2rgba(_initColor),
    },
  };
  selectedColor.value = _initColor;
  emit("update:modelValue", _initColor);
});
</script>

<style lang="scss" scoped>
:deep(.vacp-color-picker) {
  @apply bg-inherit text-inherit;
  .vacp-copy-button {
    @apply hidden;
  }
  .vacp-color-space {
    @apply rounded-sm ring-1;
  }
  .vacp-range-input-group {
    @apply col-span-2;
  }
  .vacp-color-inputs {
    @apply hidden;
  }
}
</style>
