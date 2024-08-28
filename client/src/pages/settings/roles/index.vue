<template>
  <div
    class="relative flex h-full max-w-full flex-auto flex-col rounded-2xl"
    :class="[transitioning && 'overflow-hidden']"
  >
    <transition
      enter-from-class="opacity-0"
      leave-to-class="opacity-0"
      enter-active-class="transition duration-300 delay-300"
      leave-active-class="transition duration-300"
      @before-leave="transitioning = true"
      @after-enter="transitioning = false"
    >
      <template v-if="editor.show">
        <Editor
          :modelValue="editor.data"
          v-model:loading="loading"
          v-model:loadingMessage="loadingMessage"
          @update:modelValue="onRoleUpdate"
          @back="closeEditor"
        />
      </template>
      <template v-else>
        <RolesList
          :roles="roles"
          v-model:loading="loading"
          v-model:loadingMessage="loadingMessage"
          :search="search"
          @update:search="onSearch"
          @edit="openEditor"
          @delete="(role) => openDialog(role, 'delete')"
        />
      </template>
    </transition>
    <TDialog :modelValue="dialog.show" persistent>
      <Deleter
        v-if="dialog.type == 'delete' && $guard.can(['roles_delete'])"
        v-model:modelValue="dialog.data"
        @delete="onRoleDelete"
        @close="dialog.show = false"
      />
    </TDialog>
  </div>
</template>

<script setup>
import { inject, onMounted, reactive, ref } from "vue";
import { Helpers } from "@/scripts";
import { useRoleStore } from "@/stores";
import { useGuard } from "@/plugins/composables";
import RolesList from "./list/index.vue";
import Editor from "./editor/index.vue";
import Deleter from "./editor/delete.vue";
import { useRoute } from "vue-router";

const $api = inject("$api");
const $guard = useGuard();

const route = useRoute();
const roleStore = useRoleStore();
const search = ref("");
const roles = ref([]);
const loading = ref(false);
const loadingMessage = ref("");
const transitioning = ref(false);
const roleDelete = ref(null);

const pagination = ref({
  page: 1,
  pages: 1,
  total: 0,
  limit: 25,
  offset: 0,

  sort: "name",
  order: "asc",

  column: "name",
});

const editor = reactive({
  show: false,
  data: null,
});

const dialog = reactive({
  show: false,
  data: null,
  type: null,
});

const onSearch = (data) => {
  search.value = data ?? "";
  pagination.value.limit = 25;
  pagination.value.offset = 0;
  pagination.value.column = "name";
  pagination.value.order = "asc";
  pagination.value.sort = "name";
  dialog.show = false;
  searchRoles();
};

const searchRoles = () => {
  loading.value = true;
  loadingMessage.value = "Loading roles, please wait...";

  $api
    .get(`/roles`, {
      params: {
        search: search.value,
        limit: pagination.value.limit,
        offset: pagination.value.offset,
        column: pagination.value.column,
        order: pagination.value.order,
        orderBy: pagination.value.sort,
      },
    })
    .then((response) => {
      pagination.value.total = response.data.count;
      roles.value = response.data.data;
    })
    .finally(() => {
      loading.value = false;
    });
};

const openDialog = (data, type) => {
  dialog.data = data;
  dialog.type = type;
  dialog.show = true;
};

const openEditor = (role) => {
  editor.data = role;
  editor.show = true;
};

const closeEditor = () => {
  editor.data = null;
  loading.value = false;
  editor.show = false;
};

const onRoleUpdate = (role) => {
  Helpers.updateModel(roles.value, role);
  loading.value = false;
  editor.show = false;
};

const onRoleDelete = (role) => {
  roles.value = roles.value.filter((item) => item.id != role.id);
  loading.value = false;
  dialog.show = false;
};

onMounted(() => {
  searchRoles();
});
</script>
