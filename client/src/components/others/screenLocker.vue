<template>
  <slot>
    <TButton
      :icon="icon"
      :iconSize="iconSize"
      :label="label"
      :iconClass="iconClass"
      class="my-1 rounded-full"
      v-bind="$attrs"
      @click="lockScreen"
    />
    <TDialog v-model="pinSetter.show">
      <TCard class="relative w-[95dvw] max-w-xs bg-page-background">
        <span class="absolute right-2 top-2">
          <TButton
            icon="close"
            iconSize="sm"
            class="aspect-square rounded-full p-1"
            @click="pinSetter.show = false"
          />
        </span>
        <TCardBody class="grid gap-5">
          <TPin
            v-model="pinSetter.pin.value"
            @update:modelValue="setPin"
            :error="pinSetter.pin.error"
            :errorMessage="pinSetter.pin.errorMessage"
            hint="PIN"
            confirmation
            allowKeystroke
          />
        </TCardBody>
      </TCard>
    </TDialog>
  </slot>
</template>

<script setup>
import { ref } from "vue";
import { InputField } from "@/scripts";
import { useAuthStore } from "@/stores";

const authStore = useAuthStore();

const props = defineProps({
  label: {
    type: [String, Number],
    default: null,
  },
  icon: {
    type: String,
    default: "lock",
  },
  iconSize: {
    type: String,
    default: "md",
    validator: (val) => {
      return (
        ["xs", "sm", "md", "lg", "xl", "2xl", "3xl", "4xl", "5xl"].indexOf(
          val
        ) > -1
      );
    },
  },
  iconClass: {
    type: [String, Array],
    default: () => [],
  },
});

const pinSetter = ref({
  show: false,
  pin: new InputField().setName("PIN").setRules("required|maxLength:4"),
});

const lockScreen = () => {
  if (!!authStore.pin) {
    authStore.lock();
  } else {
    pinSetter.value.pin.reset();
    pinSetter.value.show = true;
  }
};

const setPin = () => {
  if (valdiate()) {
    authStore.setPin(pinSetter.value.pin.value);
    pinSetter.value.show = false;
    pinSetter.value.pin.reset();
    authStore.lock();
  }
};

const valdiate = () => {
  pinSetter.value.pin.validate();

  return !pinSetter.value.pin.error;
};
</script>
