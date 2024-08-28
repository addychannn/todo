<template>
  <TCard class="relative max-h-[95dvh] w-[95dvw] max-w-sm">
    <slot name="before"> </slot>
    <slot name="header">
      <TCardHeader>
        <TCardTitle> Create User </TCardTitle>
        <TButton
          icon="close"
          iconSize="sm"
          class="rounded-full p-1"
          @click="emit('close')"
        />
      </TCardHeader>
    </slot>
    <TCardBody class="flex min-h-[23.75rem] flex-col md:min-h-[21.55rem]">
      <TFormWizard
        v-model:tab="tab"
        :tabs="tabs"
        class="flex-auto"
        ref="wizard"
        v-bind="wizardBindings"
        @submit="addUser"
      />
    </TCardBody>
    <slot
      name="footer"
      :currentPage="tab"
      :nextPage="() => wizard?.nextPage()"
      :totalPages="tabs.length"
      :prevPage="() => wizard?.prevPage()"
      :addUser="addUser"
    >
      <TCardFooter class="flex items-center justify-end gap-1">
        <TButton
          label="Prev"
          class="rounded-md px-3 py-1"
          :disabled="tab <= 0"
          @click="wizard?.prevPage()"
        />
        <TButton
          v-if="tab < tabs.length - 1"
          label="Next"
          class="rounded-md px-3 py-1"
          @click="wizard?.nextPage()"
        />
        <TButton
          v-else
          label="Save"
          class="rounded-md bg-primary bg-glossy px-3 py-1 text-light"
          @click="addUser"
        />
        <TButton
          label="cancel"
          class="rounded-md px-3 py-1"
          @click="emit('close')"
        />
      </TCardFooter>
    </slot>
    <slot name="after"></slot>
    <TInnerLoading :active="loading" :text="loadingMessage" />
  </TCard>
</template>

<script setup>
import { computed, defineAsyncComponent, inject, ref, toValue } from "vue";
import { Helpers, Uploader, notify } from "@/scripts";

const $api = inject("$api");

const props = defineProps({
  api: String,
  avatarApi: String,
  modules: {
    type: Object,
    default: () => ({
      profile: {},
      avatar: {},
      account: {},
      roles: {},
    }),
  },
  wizardBindings: {
    type: Object,
    default: () => ({}),
  },
});

const emit = defineEmits(["close", "created"]);

const tabPages = [
  {
    label: "Profile",
    name: "profile",
    icon: "person",
    component: defineAsyncComponent(() => import("./profile.vue")),
  },
  {
    label: "Image",
    name: "avatar",
    icon: "photo_camera",
    component: defineAsyncComponent(() => import("./avatar.vue")),

    // bgClass: "bg-black",
    // activeClass: "bg-rose-700",
    // activeIconClass: "text-light",
    // activeLabelClass: "text-rose-700",
    // idleClass: "bg-gray-400/75",
    // idleIconClass: "text-dark",
    // idleLabelClass: "text-foreground",
  },
  {
    label: "Account",
    name: "account",
    icon: "account_circle",
    component: defineAsyncComponent(() => import("./account.vue")),
    // bgClass: "bg-black",
  },
  {
    label: "Roles",
    name: "roles",
    icon: "security",
    component: defineAsyncComponent(() => import("./roles.vue")),
  },
];

const tab = ref(0);
const wizard = ref(null);
const loading = ref(false);
const loadingMessage = ref("");

const tabs = computed(() => {
  let t = [];
  Object.keys(props.modules).forEach((m) => {
    let tmp = tabPages.find((tp) => tp.name == m);
    t.push(Object.assign({}, tmp, { bindings: props.modules[m] }));
  });
  return t;
});
const editor = computed(() => toValue(wizard.value?.editor) ?? {});

const addUser = () => {
  if (wizard.value?.validate()) {
    loading.value = true;
    loadingMessage.value = "Saving user data...";

    $api
      .post(props.api, getPayload())
      .then((response) => {
        console.log(editor.value?.avatar?.value);
        if (!!editor.value?.avatar?.value) {
          uploadAvatar(response.data.data.id);
        } else {
          wizard.value?.reset();
          emit("created", response.data.data);
          notify({
            title: "Success!",
            type: "positive",
            text: response.data.message,
          });
          loading.value = false;
        }
      })
      .catch((error) => {
        wizard.value?.onError(error.response?.data?.errors ?? []);
        Helpers.onRequestError(error, editor.value);
        loading.value = false;
      });
  }
};

const uploadAvatar = async (userID) => {
  if (!!editor.value?.avatar?.value) {
    try {
      loading.value = true;
      loadingMessage.value = "Uploading image, please wait...";

      let _uploader = new Uploader({
        axios: $api,
        url: `${props.avatarApi}/${userID}`,
      });

      const response = await _uploader.upload(
        editor.value.avatar.value,
        null,
        null,
        null,
        (val) => {
          loadingMessage.value = `Uploading image, please wait... (${Math.round(
            val * 100
          )}%)`;
        }
      );
      wizard.value?.reset();
      emit("created", response.data.data);
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

const getPayload = () => {
  let data = {};
  let _except = ["avatar"];
  Object.keys(editor.value).forEach((key) => {
    if (!_except.includes(key)) {
      let tmp = {
        [key]: editor.value[key].value,
      };
      if (key == "roles") {
        tmp = {
          [key]: editor.value[key].value.map((r) => r.id),
        };
      }
      Object.assign(data, tmp);
    }
  });
  return data;
};
</script>
