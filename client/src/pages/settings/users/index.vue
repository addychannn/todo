<template>
  <div
    class="relative flex min-h-full max-w-full flex-auto flex-col rounded-2xl"
  >
    <transition
      enter-from-class="opacity-0 blur-md"
      leave-to-class="opacity-0 blur-md"
      enter-active-class="transition duration-300 delay-300"
      leave-active-class="transition duration-300"
      @before-leave="transitioning = true"
      @after-enter="transitioning = false"
    >
      <Editor
        v-if="!!userID"
        :user="user"
        @update:user="(val) => onUserUpdate(val, false)"
      />
      <UserList
        v-else
        :users="users"
        :newUsers="newUsers"
        v-model:search="searchParams.search"
        v-model:roles="searchParams.roles"
        v-model:loading="loading"
        v-model:loadingMessage="loadingMessage"
        v-model:pagination="pagination"
        @create="openDialog(null, 'creator')"
        @update:search="searchUsers"
        @update:roles="searchUsers"
        @paginate="searchUsers"
        @reset="resetParams(), resetPagination(), searchUsers()"
      />
    </transition>
    <TInnerLoading :active="loading" :text="loadingMessage" />
    <TDialog v-model="dialog.show">
      <Creator
        v-if="dialog.type == 'creator' && $guard.can(['users_add'])"
        api="/users"
        avatarApi="/users/avatar"
        @created="(val) => onUserUpdate(val, true)"
        @close="closeDialog"
      />
    </TDialog>
  </div>
</template>

<script setup>
import { reactive, inject, onMounted, ref, computed, watch } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useUserStore } from "@/stores";
import { Helpers } from "@/scripts";
import { useSearcher, useGuard } from "@/plugins/composables";
import UserList from "./list/index.vue";
import Editor from "./editor/index.vue";
import Creator from "./editor/createUser/index.vue";

const $api = inject("$api");
const $guard = useGuard();
const $route = useRoute();
const $router = useRouter();
const userStore = useUserStore();

const { searcher, pagination, resetPagination, readRouteQuery } = useSearcher(
  "/users",
  {
    appendToUrl: true,
  }
);

const initialSearchParams = {
  search: {
    name: null,
    username: null,
    email: null,
  },
  roles: [],
};

const searchParams = ref({ ...initialSearchParams });
const users = ref([]);
const newUsers = ref([]);
const user = ref(null);
const loading = ref(false);
const loadingMessage = ref("");
const loaded = ref(false);

const dialog = ref({
  show: false,
  data: null,
  type: null,
});

const userID = computed(() => $route.params.id);

const openDialog = (data, type) => {
  dialog.value.data = data;
  dialog.value.type = type;
  dialog.value.show = true;
};

const closeDialog = () => {
  dialog.value.show = false;
  dialog.value.data = null;
};

const resetParams = () => {
  searchParams.value = Object.assign(
    {},
    searchParams.value,
    initialSearchParams
  );
};

const searchUsers = () => {
  loading.value = true;
  loadingMessage.value = "Searching users, please wait...";

  searcher(searchParams.value)
    .then((response) => {
      pagination.value.total = response.data.count;
      users.value = response.data.data;
      newUsers.value = [];
    })
    .finally(() => {
      loading.value = false;
      loaded.value = true;
    });
};

const getUser = (id) => {
  loading.value = true;
  loadingMessage.value = "Loading user, please wait...";

  $api
    .get(`/users/i/${id}`)
    .then((response) => {
      user.value = response.data.data;
    })
    .finally(() => {
      loading.value = false;
      loaded.value = true;
    });
};

const loadUser = () => {
  user.value = null;
  if (!userID.value && users.value.length <= 0 && newUsers.value.length <= 0) {
    searchUsers();
  }
  if (!!userID.value) {
    user.value =
      users.value.find((item) => item.id == userID.value) ||
      newUsers.value.find((item) => item.id == userID.value);

    if (!user.value) {
      getUser(userID.value);
    }
  }
};

const onUserUpdate = (new_user, open = false) => {
  Helpers.updateModel(
    users.value,
    new_user,
    users.value.length <= 0 && newUsers.value.length <= 0
  );
  let newUser = !users.value.find((item) => item.id == new_user.id);
  if (newUser) {
    Helpers.updateModel(newUsers.value, new_user);
  }
  user.value = Object.assign({}, new_user);
  if (open) {
    $router.push({ to: "settings-users", params: { id: new_user.id } });
  }
  closeDialog();
};

watch($route, (val) => {
  if (loaded.value) loadUser();
});

onMounted(() => {
  readRouteQuery(searchParams.value);
  loadUser();
});
</script>
