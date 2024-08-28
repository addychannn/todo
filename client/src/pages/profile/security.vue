<template>
  <div class="relative rounded-md">
    <div class="flex flex-col justify-start gap-2">
      <ProfileItem
        label="Password"
        value="******"
        class="select-none"
        :class="{ 'cursor-pointer': $guard.can('self_change-password') }"
        :icon="$guard.can('self_change-password') ? 'edit' : null"
        @click="openPasswordEditor"
      >
      </ProfileItem>
      <ProfileItem
        label="Lock Screen PIN"
        value="******"
        class="select-none border-none"
        :icon="null"
        :class="[!user.verified && 'cursor-pointer']"
        disableFocus
      >
        <div class="flex w-full flex-col items-center">
          <div
            v-if="!!user.pin"
            class="self-start py-2 font-semibold leading-none"
          >
            ****
          </div>
          <TPin
            v-else-if="!!user.verified"
            @update:modelValue="user.setPin"
            confirmation
            allowKeystroke
          />
        </div>
        <template v-if="!!user.pin" #icon>
          <TButton
            icon="lock_reset"
            label="Reset"
            class="rounded-full p-1"
            @click="user.resetPin()"
          />
        </template>
      </ProfileItem>
    </div>
    <TDialog v-if="$guard.can('self_change-password')" v-model="passwordChange">
      <Password @close="passwordChange = false" />
    </TDialog>
  </div>
</template>

<script setup>
import { ref, defineAsyncComponent } from "vue";
import { useVModel } from "@vueuse/core";
import { useGuard } from "@/plugins/composables";

const $guard = useGuard();

const ProfileItem = defineAsyncComponent(() => import("./profileItem.vue"));
const Password = defineAsyncComponent(() => import("./password.vue"));

const props = defineProps({
  modelValue: Object,
});

const emit = defineEmits(["update:modelValue"]);

const user = useVModel(props, "modelValue", emit);

const passwordChange = ref(false);

const openPasswordEditor = () => {
  if (!!user.value.verified && $guard.can("self_change-password")) {
    passwordChange.value = true;
  }
};
</script>
