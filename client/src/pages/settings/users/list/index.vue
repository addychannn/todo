<template>
  <div
    class="relative flex min-h-full max-w-full flex-auto flex-col rounded-2xl"
  >
    <div class="px-2">
      <div class="py-3 text-3xl font-bold md:text-5xl">User Management</div>
      <div class="flex flex-col items-center gap-1 md:flex-row">
        <div class="flex-auto">
          <TButton
            v-if="$guard.can(['users_add'])"
            icon="person_add_alt"
            label="Add User"
            class="rounded-xl border border-dark bg-light px-3 py-1 text-dark shadow-sm transition hover:shadow-md"
            focusDisabled
            @click="emit('create')"
          />
        </div>
        <TPagination
          v-model="_pagination.page"
          v-model:limit="_pagination.limit"
          v-model:offset="_pagination.offset"
          v-model:totalPage="_pagination.pages"
          :total="_pagination.total"
          :maxPages="1"
          hideEllipsis
          class="justify-center gap-1"
          linkClass="aspect-square w-6 p-1 text-sm leading-none flex items-center justify-center rounded-md"
          @paginate="(val) => emit('paginate', val)"
        />
        <TButton
          icon="refresh"
          class="rounded-full p-1"
          @click="emit('reset')"
        />
      </div>
    </div>
    <div class="flex flex-auto flex-col gap-1 p-3">
      <table class="w-full">
        <thead class="hidden md:table-header-group">
          <tr class="border-b border-foreground/25 align-text-top">
            <th class="w-64 py-0.5 text-start">
              <SearchableColumn
                label="User"
                columnName="name"
                :modelValue="search.name"
                @update:modelValue="(val) => onColumnSearch('name', val)"
                v-model:order="_pagination.order"
                :currentColumn="_pagination.sort"
                searchable
                sortable
                @sort="onSort"
              />
            </th>
            <th class="w-48 py-0.5 text-start">
              <SearchableColumn
                label="Username"
                columnName="username"
                :modelValue="search.username"
                @update:modelValue="(val) => onColumnSearch('username', val)"
                v-model:order="_pagination.order"
                :currentColumn="_pagination.sort"
                searchable
                sortable
                @sort="onSort"
              />
            </th>
            <th class="w-48 py-0.5 text-start">
              <SearchableColumn
                label="Email"
                columnName="email"
                :modelValue="search.email"
                @update:modelValue="(val) => onColumnSearch('email', val)"
                v-model:order="_pagination.order"
                :currentColumn="_pagination.sort"
                searchable
                sortable
                @sort="onSort"
              />
            </th>
            <th colspan="2" class="py-0.5 text-start">
              <RoleFilter
                label="Roles"
                columnName="roles"
                :modelValue="roles"
                @update:modelValue="(val) => emit('update:roles', val)"
                v-model:order="_pagination.order"
                :currentColumn="_pagination.sort"
                searchable
                @sort="onSort"
              />
            </th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="!_loading && users.length <= 0 && newUsers.length <= 0">
            <td
              colspan="100%"
              class="p-3 text-center font-semibold italic text-gray-400"
            >
              No user found!
            </td>
          </tr>
          <template v-if="newUsers.length > 0">
            <tr>
              <td colspan="100%" class="px-2 font-semibold text-gray-400">
                New Users
              </td>
            </tr>
            <template v-for="user in newUsers" :key="user.id">
              <UserItem :user="user" />
            </template>
            <tr>
              <td colspan="100%" class="bg-foreground/25 py-1"></td>
            </tr>
          </template>
          <template v-for="user in users" :key="user.id">
            <UserItem :user="user" />
          </template>
        </tbody>
      </table>
    </div>
    <div
      class="flex items-center justify-center pb-3"
      :class="[transitioning && '!hidden']"
    >
      <TPagination
        v-model="_pagination.page"
        v-model:limit="_pagination.limit"
        v-model:offset="_pagination.offset"
        v-model:totalPage="_pagination.pages"
        :total="_pagination.total"
        class="justify-center gap-1"
        linkClass="aspect-square w-6 p-1 text-sm leading-none flex items-center justify-center rounded-md"
        @paginate="(val) => emit('paginate', val)"
      />
    </div>
  </div>
</template>

<script setup>
import { useVModel } from "@vueuse/core";
import { useGuard } from "@/plugins/composables";
import UserItem from "./userItem.vue";
import RoleFilter from "./roleFilter.vue";

const $guard = useGuard();

const props = defineProps({
  users: [Object, Array],
  newUsers: {
    type: [Object, Array],
    default: () => [],
  },
  search: {
    type: Object,
    default: {
      name: null,
      username: null,
      email: null,
    },
  },
  roles: {
    type: Array,
    default: () => [],
  },
  pagination: {
    type: Object,
    default: () => ({
      page: 1,
      pages: 1,
      total: 0,
      limit: 25,
      offset: 0,

      sort: "name",
      order: "asc",
    }),
  },
  loading: {
    type: Boolean,
    default: false,
  },
  loadingMessage: {
    type: String,
    default: null,
  },
  transitioning: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits([
  "update:loading",
  "update:loadingMessage",
  "update:search",
  "update:roles",
  "update:pagination",
  "search",
  "reset",
  "create",
  "paginate",
]);

const _loading = useVModel(props, "loading", emit);
const _loadingMessage = useVModel(props, "loadingMessage", emit);
const _pagination = useVModel(props, "pagination", emit);

const onColumnSearch = (key, val) => {
  emit("update:search", Object.assign({}, props.search, { [key]: val }));
};

const onSort = (val) => {
  _pagination.value.sort = val;
  emit("paginate");
};
</script>
