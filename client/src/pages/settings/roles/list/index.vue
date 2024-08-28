<template>
  <div
    class="relative flex min-h-full max-w-full flex-auto flex-col rounded-2xl"
  >
    <div class="px-2">
      <div class="py-3 text-3xl font-bold md:text-5xl">Roles</div>
      <div class="flex flex-col items-center gap-1 md:flex-row">
        <div class="flex-auto">
          <TButton
            v-if="$guard.can('roles_add')"
            icon="add_moderator"
            label="Add Role"
            class="rounded-xl border border-dark bg-light px-3 py-1 text-dark shadow-sm transition hover:shadow-md"
            focusDisabled
            @click="emit('edit', null)"
          />
        </div>
        <TInput
          v-model="tmpSearch"
          label="Search"
          innerClass="bg-light text-dark pr-0 overflow-hidden"
          @keyup.enter="emit('update:search', tmpSearch ?? null)"
          clearable
        >
          <template #append>
            <TButton
              v-if="!!tmpSearch"
              icon="close"
              iconSize="sm"
              class="rounded-full"
              @click="emit('update:search', null)"
            />
            <TButton
              icon="search"
              class="h-full bg-primary bg-glossy px-2 text-light"
              @click="emit('update:search', tmpSearch ?? null)"
            />
          </template>
        </TInput>
      </div>
    </div>
    <div class="flex flex-auto flex-col gap-1 p-3">
      <table class="w-full">
        <thead class="hidden md:table-header-group">
          <tr class="border-b border-foreground/25">
            <th class="w-32 px-2 py-0.5 text-start">Name</th>
            <th class="px-2 py-0.5 text-start">Description</th>
            <th class="w-32 px-2 py-0.5 text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
          <template v-if="!_loading && !roles.length">
            <tr>
              <td
                colspan="100%"
                class="text-center text-lg font-semibold italic text-gray-400"
              >
                No roles found!
              </td>
            </tr>
          </template>
          <template v-for="role in roles" :key="role.id">
            <tr
              class="flex cursor-pointer select-none flex-col transition-all odd:bg-dark/5 hover:scale-[1.01] hover:bg-dark/10 dark:odd:bg-light/5 dark:hover:bg-light/10 md:table-row"
              @click="emit('edit', role)"
            >
              <td class="p-1 md:px-3 md:py-2">
                {{ role.name }}
              </td>
              <td class="p-1 md:px-3 md:py-2">
                <div class="line-clamp-4 text-xs md:line-clamp-1">
                  {{ role.description }}
                </div>
              </td>
              <td class="p-1 md:px-3 md:py-2">
                <div
                  class="flex items-center justify-end gap-1 md:justify-center"
                >
                  <TButton
                    v-if="!role.protected && $guard.can(['roles_delete'])"
                    :icon="$md ? 'delete' : null"
                    :label="$md ? null : 'Delete'"
                    focusClass="bg-negative"
                    class="rounded-lg px-3 py-1 uppercase md:aspect-square md:p-1"
                    @click.prevent.stop="emit('delete', role)"
                  />
                  <TIcon
                    v-if="role.protected"
                    name="lock"
                    class="text-positive drop-shadow-md dark:text-foreground"
                  />
                </div>
              </td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>
    <TInnerLoading :active="_loading" :text="_loadingMessage" />
  </div>
</template>

<script setup>
import { inject, ref, watch } from "vue";
import { useVModel } from "@vueuse/core";
import { useGuard } from "@/plugins/composables";

const $screen = inject("$screen");

const $md = $screen.value.greaterOrEqual("md");
const $guard = useGuard();

const props = defineProps({
  roles: Array,
  search: {
    type: [String, Number],
    default: "",
  },
  loading: {
    type: Boolean,
    default: false,
  },
  loadingMessage: {
    type: String,
    default: null,
  },
});

const emit = defineEmits([
  "update:loading",
  "update:loadingMessage",
  "update:search",
  "edit",
  "delete",
]);
const tmpSearch = ref(props.search);
const _search = useVModel(props, "search", emit);
const _loading = useVModel(props, "loading", emit);
const _loadingMessage = useVModel(props, "loadingMessage", emit);

watch(
  () => props.search,
  (val) => {
    tmpSearch.value = val;
  }
);
</script>
