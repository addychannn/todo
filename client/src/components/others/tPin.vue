<template>
  <div
    class="flex flex-col items-center justify-center gap-5 outline-none"
    tabindex="0"
    ref="pinWrapper"
    @keyup="onKeyup"
  >
    <div class="relative flex items-center justify-center">
      <slot name="display" :pin="pin">
        <div
          class="grid gap-2 text-center text-2xl font-bold"
          :class="[error && 'animate-shake text-negative']"
          :style="{ gridTemplateColumns: `repeat(${length}, minmax(0, 1fr))` }"
        >
          <div
            v-for="len in length"
            :key="`${len}`"
            class="aspect-square w-10 border-b-4 transition-colors"
            :class="[
              !!pin?.[len - 1] ? 'border-foreground/75' : 'border-gray-400/50',
              error && '!border-negative',
            ]"
          >
            {{ pin?.[len - 1] ?? "" }}
          </div>
        </div>
      </slot>
      <div
        v-if="(error != null && errorMessage != null) || hint != null"
        class="absolute inset-x-0 bottom-0 translate-y-full px-2 pt-1"
      >
        <transition
          enterFromClass="opacity-0 -translate-y-full"
          leaveToClass="opacity-0 -translate-y-full"
          enterActiveClass="transition duration-300"
          leaveActiveClass="transition duration-300"
        >
          <div
            v-if="error"
            class="absolute inset-x-0 top-0.5 text-center text-xs text-negative"
          >
            {{ errorMessage }}
          </div>
          <div
            v-else
            class="absolute inset-x-2 top-1 text-center text-[11px] leading-none text-gray-400"
          >
            <slot name="hint" :hint="hint">
              {{ hint }}
            </slot>
          </div>
        </transition>
      </div>
    </div>
    <div class="grid gap-2">
      <div class="grid grid-cols-3 gap-3">
        <div
          v-for="upin in pins"
          :key="`keypad_${upin}_pin`"
          class="flex items-center justify-center"
        >
          <TButton
            :label="`${upin}`"
            class="aspect-square w-12 rounded-full border border-foreground/25"
            @click="setPinValue(upin)"
          />
        </div>
        <div
          class="col-span-3 flex items-center gap-3"
          :class="[!confirmation && 'flex-row-reverse']"
        >
          <div class="after flex items-center justify-center">
            <TButton
              icon="backspace"
              iconSize="sm"
              :disabled="pin?.length <= 0"
              class="aspect-square w-12 rounded-full"
              @click="backspace"
            />
          </div>
          <div class="flex items-center justify-center">
            <TButton
              :label="`${zero}`"
              class="aspect-square w-12 rounded-full border border-foreground/25"
              @click="setPinValue(zero)"
            />
          </div>
          <div class="flex items-center justify-center">
            <TButton
              v-if="confirmation"
              icon="check"
              iconSize="sm"
              :disabled="
                pin == null || (pin?.length >= 0 && pin?.length < length)
              "
              class="aspect-square w-12 rounded-full"
              @click="setPin"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import { computed, ref } from "vue";
import { useFocus } from "@vueuse/core";
import { Helpers } from "@/scripts";

const props = defineProps({
  modelValue: String,
  length: {
    type: Number,
    default: 4,
  },
  error: {
    type: Boolean,
    default: null,
  },
  errorMessage: {
    type: String,
    default: "",
  },
  hint: {
    type: String,
    default: "",
  },
  confirmation: {
    type: Boolean,
    default: false,
  },
  allowKeystroke: {
    type: Boolean,
    default: false,
  },
  randomize: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(["update:modelValue"]);

const pin = ref(props.modelValue);
const pinWrapper = ref(null);
const zero = computed(() => {
  let zero = 0;
  if (props.randomize) {
    zero = Helpers.getRandomInt(1, 9);
  }
  return zero;
});
const pins = computed(() => {
  let _pins = Array.from({ length: 9 }, (_, i) => i + 1);
  if (props.randomize) {
    _pins = _pins
      .map((value) => ({ value, sort: Math.random() }))
      .sort((a, b) => a.sort - b.sort)
      .map(({ value }) => value);
    let index = _pins.indexOf(zero.value);
    _pins[index] = 0;
  }
  return _pins;
});

const { focused } = useFocus(pinWrapper, { initialValue: true });

const setPinValue = (digit) => {
  let reg = /^\d+$/;
  if (reg.test(digit) && `${pin.value ?? ""}`.length < props.length) {
    pin.value = `${pin.value ?? ""}${digit}`;
    if (!props.confirmation) {
      emit("update:modelValue", pin.value);
    }
  }
};

const backspace = () => {
  pin.value = pin.value.slice(0, -1);
};

const setPin = () => {
  if (props.confirmation && pin.value.length < props.length) return;
  emit("update:modelValue", pin.value);
};

const onKeyup = (evt) => {
  if (props.allowKeystroke) {
    if (evt.key.toLowerCase() == "enter") {
      setPin();
    } else if (evt.key.toLowerCase() == "backspace") {
      backspace();
    } else if (!isNaN(evt.key)) {
      setPinValue(evt.key);
    }
  }
};
</script>
