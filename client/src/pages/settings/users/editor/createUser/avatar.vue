<template>
  <TFormWizardTab v-model:fields="fields">
    <template #default="{ editor }">
      <template v-if="!!editor.avatar">
        <div class="relative" :class="[editor.avatar.error != null && 'mb-5']">
          <div class="flex items-center gap-1">
            <TButton
              icon="add_photo_alternate"
              label="Upload"
              class="rounded-md pl-1 pr-2"
              :class="[mode == 'upload' && 'bg-primary text-light']"
              @click="mode = 'upload'"
            />
            <TButton
              icon="add_a_photo"
              label="Take Photo"
              class="rounded-md pl-1 pr-2"
              :class="[mode == 'camera' && 'bg-primary text-light']"
              @click="mode = 'camera'"
            />
          </div>

          <TCamera
            v-if="mode == 'camera'"
            v-model="editor.avatar.value"
            wrapperClass="h-[13.546rem]"
          />
          <TImageSelect
            v-if="mode == 'upload'"
            v-model="editor.avatar.value"
            class="h-[13.546rem] w-full rounded-b-lg"
          >
            <template #overlay> </template>
            <template #default="{ src }">
              <div class="flex h-full w-full items-center justify-center p-3">
                <TImage
                  :src="src"
                  class="aspect-square h-full rounded-full border-2 border-gray-400 bg-light shadow-md"
                >
                  <template #error>
                    <div class="flex w-full items-center justify-center">
                      <TIcon
                        name="account_circle"
                        class="text-gray-500"
                        size="2xl"
                      />
                    </div>
                  </template>
                </TImage>
              </div>
            </template>
          </TImageSelect>
          <div
            v-if="editor.avatar.error != null"
            class="absolute inset-x-0 bottom-0 translate-y-full px-2 pt-1 text-[11px] leading-none"
          >
            <transition
              enter-from-class="-translate-y-full opacity-0"
              leave-to-class="-translate-y-full opacity-0"
              enter-active-class="transition duration-300"
              leave-active-class="transition duration-300"
            >
              <div
                v-if="editor.avatar.error"
                class="absolute left-2 right-0 top-0.5 mt-0.5 text-[11px] font-semibold leading-none"
              >
                <span
                  class="rounded-full border-negative px-1 leading-tight text-negative dark:border dark:bg-negative/75 dark:text-light"
                >
                  {{ editor.avatar.errorMessage }}
                </span>
              </div>
            </transition>
          </div>
        </div>
      </template>
    </template>
  </TFormWizardTab>
</template>

<script setup>
import { ref } from "vue";
import { InputField } from "@/scripts";
const props = defineProps({});

const fields = ref({
  avatar: new InputField(null).setName("Profile Image").setRules("required"),
});
const mode = ref("upload");
</script>
