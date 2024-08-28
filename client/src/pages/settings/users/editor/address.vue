<template>
  <TCard
    class="relative h-[95dvh] w-[95dvw] max-w-md bg-background-accent text-foreground sm:max-h-96"
  >
    <TCardHeader class="bg-page-background">
      <TCardTitle> Address Editor </TCardTitle>
      <TButton
        icon="close"
        iconSize="sm"
        class="aspect-square rounded-full p-0.5"
        @click="emit('close')"
        :disabled="page.name != null"
      />
    </TCardHeader>
    <TCardBody :class="[transitioning ? 'overflow-hidden' : '']">
      <transition
        enter-from-class="opacity-0 blur-md"
        leave-to-class="opacity-0 blur-md"
        enter-active-class="transition duration-300 delay-300"
        leave-active-class="transition duration-300"
        @before-leave="transitioning = true"
        @after-enter="transitioning = false"
      >
        <AddressEditor
          v-if="page.name == 'editor'"
          :address="page.data"
          :api="api"
          v-model:loading="loading"
          v-model:loadingMessage="loadingMessage"
          nullableType
          @update:user="onUserUpdate"
          @cancel="openPage(null, null)"
        />

        <AddressDelete
          v-else-if="page.name == 'deleter'"
          :address="page.data"
          :api="api"
          v-model:loading="loading"
          v-model:loadingMessage="loadingMessage"
          @update:user="onUserUpdate"
          @cancel="openPage(null, null)"
        />

        <MainAddressChanger
          v-else-if="page.name == 'changer'"
          :address="page.data"
          :primary="primaryAddress"
          :api="api"
          v-model:loading="loading"
          v-model:loadingMessage="loadingMessage"
          @update:user="onUserUpdate"
          @cancel="openPage(null, null)"
        />

        <AddressList
          v-else
          :addresses="user.profile?.addresses ?? []"
          @create="openPage(null, 'editor')"
          @edit="(val) => openPage(val, 'editor')"
          @delete="(val) => openPage(val, 'deleter')"
          @change="(val) => openPage(val, 'changer')"
        />
      </transition>
    </TCardBody>
    <TCardFooter class="flex items-center justify-end gap-1 bg-page-background">
      <TButton
        :disabled="page.name != null"
        label="Ok"
        class="w-16 rounded-full px-3 py-1"
        @click="emit('close')"
      />
    </TCardFooter>
    <TInnerLoading :active="loading" :text="loadingMessage" />
  </TCard>
</template>

<script setup>
import { computed, defineAsyncComponent, ref } from "vue";
import AddressEditor from "./address/editor.vue";
import AddressList from "./address/list.vue";
import AddressDelete from "./address/delete.vue";
import MainAddressChanger from "./address/changeMain.vue";

const props = defineProps({
  user: Object,
  api: String,
});
const emit = defineEmits(["update:user", "close"]);

const loading = ref(false);
const loadingMessage = ref("");
const transitioning = ref(false);

const page = ref({
  name: null,
  data: null,
});

const primaryAddress = computed(
  () =>
    props.user.profile?.addresses?.filter((item) => item.isMain)?.[0] ?? null
);

const openPage = (data, name) => {
  page.value.data = data;
  page.value.name = name;
};

const onUserUpdate = (val) => {
  emit("update:user", val);
  openPage(null, null);
  loading.value = false;
};
</script>
