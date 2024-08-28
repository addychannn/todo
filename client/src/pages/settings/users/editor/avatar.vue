<template>
  <TCard
    class="relative max-h-[95dvh] w-[95dvw] max-w-sm bg-background-accent text-foreground"
  >
    <TCardHeader class="bg-page-background">
      <TCardTitle> Profile Picture </TCardTitle>
      <TButton
        icon="close"
        iconSize="sm"
        class="aspect-square rounded-full p-0.5"
        @click="emit('close')"
      />
    </TCardHeader>
    <TCardBody class="!p-0" :class="[transitioning && '!overflow-hidden']">
      <div
        class="relative min-h-[20.7rem] px-3 py-2"
        :class="[!transitioning && 'flex flex-col']"
      >
        <transition
          v-bind="{
            ...transitions[addImage.show ? 'toRight' : 'toLeft'],
            enterActiveClass:
              'transition-all duration-300 absolute top-2 inset-x-3',
            leaveActiveClass:
              'transition-all duration-300 absolute top-2 inset-x-3',
          }"
          @before-leave="transitioning = true"
          @after-enter="transitioning = false"
        >
          <div
            v-if="!addImage.show"
            class="flex min-h-[19.7rem] flex-auto flex-col gap-2"
          >
            <div class="flex-auto">
              <div class="grid gap-2">
                <div class="flex items-center justify-center">
                  <div
                    class="relative aspect-square w-24 overflow-hidden rounded-xl"
                  >
                    <TImage
                      :src="user.profile?.image?.thumbnails.small"
                      class="h-full w-full rounded-xl border-2 border-gray-400 bg-light shadow-md after:absolute after:inset-1 after:rounded-full after:shadow-[0_0_0_9999px_rgba(0,0,0,0.3)]"
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
                    <div
                      v-if="false"
                      class="absolute inset-x-0 bottom-0 flex items-center justify-center bg-dark/50 text-light"
                    >
                      <TIcon name="camera_alt" size="sm" />
                    </div>
                  </div>
                </div>
                <div class="flex flex-wrap items-start justify-center gap-2">
                  <template v-if="!user.profile?.images.length">
                    <div class="p-2 text-center italic text-gray-400">
                      No images found!
                    </div>
                  </template>
                  <template
                    v-for="image in user.profile?.images"
                    :key="image.id"
                  >
                    <TImage
                      :src="image.thumbnails.small"
                      class="aspect-square w-12 cursor-pointer rounded-xl border-2 border-gray-400 bg-light shadow-md outline-2 after:absolute after:inset-1 after:rounded-full after:shadow-[0_0_0_9999px_rgba(0,0,0,0.3)]"
                      :class="[
                        image.id == user.profile?.image.id &&
                          'outline outline-positive',
                        isSelected(image) && 'outline outline-primary',
                      ]"
                      @click="selectImage(image)"
                    >
                      <template #error>
                        <div class="flex w-full items-center justify-center">
                          <TIcon
                            name="account_circle"
                            class="text-gray-500"
                            size="lg"
                          />
                        </div>
                      </template>
                    </TImage>
                  </template>
                </div>
              </div>
            </div>
            <div class="flex items-center justify-center gap-2 px-3 py-2">
              <TButton
                label="Add Image"
                class="rounded-lg border-2 border-primary px-5 py-1 shadow-md"
                @click="addImage.show = true"
              />
              <TButton
                label="Use Image"
                class="rounded-lg border-2 border-primary px-5 py-1 shadow-md"
                :disabled="!selectedImage"
                @click="changeAvatar"
              />
            </div>
          </div>
          <div v-else class="flex min-h-[19.7rem] flex-auto flex-col gap-1">
            <div class="flex items-center">
              <div class="flex-auto">
                <TButton
                  icon="arrow_left"
                  label="Back"
                  class="rounded-l-xl rounded-r-md px-3 py-1"
                  @click="closeUploader"
                />
              </div>
              <TButton
                icon="file_upload"
                label="Upload"
                class="rounded-l-md rounded-r-xl px-3 py-1"
                :disabled="!addImage.file"
                @click="uploadAvatar"
              />
            </div>
            <div class="flex flex-auto items-start justify-center">
              <TImageSelect
                v-model="addImage.file"
                class="h-[17rem] w-full rounded-lg"
              >
                <template #overlay> </template>
                <template #default="{ src }">
                  <div
                    class="flex h-full w-full items-center justify-center p-3"
                  >
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
            </div>
          </div>
        </transition>
      </div>
    </TCardBody>
    <TCardFooter class="border-none"> </TCardFooter>
    <TInnerLoading :active="loading" :text="loadingMessage" />
  </TCard>
</template>

<script setup>
import { inject, ref } from "vue";
import { transitions, Uploader, notify } from "@/scripts";

const $api = inject("$api");
const props = defineProps({
  user: Object,
  api: String,
});

const emit = defineEmits(["update:user", "close"]);

const loading = ref(false);
const loadingMessage = ref("");
const transitioning = ref(false);
const selectedImage = ref(null);
const addImage = ref({
  show: false,
  file: null,
});

const closeUploader = () => {
  addImage.value.show = false;
  addImage.value.file = null;
};

const uploadAvatar = async () => {
  if (!!addImage.value.file) {
    try {
      loading.value = true;
      loadingMessage.value = "Uploading image, please wait...";

      let _uploader = new Uploader({
        axios: $api,
        url: props.api,
      });

      const response = await _uploader.upload(
        addImage.value.file,
        null,
        null,
        null,
        (val) => {
          loadingMessage.value = `Uploading image, please wait... (${Math.round(
            val * 100
          )}%)`;
        }
      );

      closeUploader();
      emit("update:user", response.data.data);
      notify({
        title: "Success!",
        type: "positive",
        text: response.data.message,
      });
    } catch (e) {
      notify({
        title: "Upload failed!",
        type: "negative",
        text: e,
      });
    } finally {
      loading.value = false;
    }
  }
};

const selectImage = (image) => {
  let isNull =
    selectedImage.value?.id == image.id ||
    image.id == props.user.profile?.image?.id;
  selectedImage.value = isNull ? null : image;
};
const isSelected = (image) => {
  return selectedImage.value?.id == image.id;
};

const changeAvatar = () => {
  if (!!selectedImage.value) {
    loading.value = true;
    loadingMessage.value = "Changing profile image, please wait...";
    $api
      .patch(props.api, {
        image: selectedImage.value.id,
      })
      .then((response) => {
        emit("update:user", response.data.data);
        notify({
          title: "Success!",
          type: "positive",
          text: response.data.message,
        });
      })
      .finally(() => {
        loading.value = false;
      });
  }
};
</script>
