<template>
  <div
    class="relative flex min-h-full max-w-full flex-auto flex-col rounded-2xl"
  >
    <div class="flex items-center gap-1 px-2">
      <div class="flex-auto pb-5 text-5xl font-bold">
        {{ isEdit ? "Edit" : "Create" }} Role
      </div>
    </div>
    <div class="flex flex-auto flex-col md:flex-row md:items-stretch">
      <div class="md:w-64">
        <div v-sticky class="md:w-64">
          <div class="px-2">
            <TButton
              class="w-full rounded-lg px-2 py-1 text-start"
              @click="emit('back')"
            >
              <div class="pointer-events-none flex items-center">
                <TIcon name="arrow_left" />
                <div class="flex-auto">Back</div>
              </div>
            </TButton>
          </div>
          <div
            class="relative overflow-hidden rounded-xl p-2"
            :class="[modelValue?.protected && 'border border-positive']"
          >
            <TInput
              :label="editor.name.name"
              v-model="editor.name.value"
              :error="editor.name.error"
              :errorMessage="editor.name.errorMessage"
              :innerClass="[
                'bg-light text-dark',
                modelValue?.protected && '!bg-gray-300',
              ]"
              @keyup.enter="saveRole"
              :disabled="modelValue?.protected || !canEdit"
            />
            <TTextArea
              :label="editor.description.name"
              v-model="editor.description.value"
              :innerClass="[
                'bg-light text-dark',
                modelValue?.protected && '!bg-gray-300',
              ]"
              :disabled="modelValue?.protected || !canEdit"
            />
            <div
              v-if="modelValue?.protected"
              class="absolute left-4 top-4 flex w-full -translate-x-1/2 -translate-y-1/2 -rotate-45 items-center justify-center bg-positive px-2 py-1 text-light"
            >
              <span class="flex items-center justify-center px-4">
                <TIcon name="lock" class="rotate-45" size="sm" />
                <TToolTip arrow>
                  <span class="text-sm">
                    Protected Role - Modifications are limited
                  </span>
                </TToolTip>
              </span>
            </div>
          </div>
          <div v-if="$md" class="p-0 pl-2">
            <div
              class="relative flex items-center rounded-lg border border-foreground/25 bg-page-background p-2 shadow-md md:rounded-r-none"
            >
              <div class="flex-auto text-lg font-semibold uppercase">
                Permissions
              </div>
              <TIcon name="arrow_right" />
              <div
                class="absolute inset-y-0 right-0 translate-x-1/2 bg-page-background px-1"
              ></div>
            </div>
          </div>
          <div class="grid gap-2 p-2">
            <TButton
              v-if="canEdit"
              label="Save"
              class="w-full rounded-full bg-primary bg-glossy px-5 py-2 text-light shadow-md"
              @click="saveRole"
            />
          </div>
        </div>
      </div>
      <div
        class="flex flex-auto flex-col gap-2 rounded-lg border border-foreground/25 bg-page-background p-2 shadow-md md:w-[calc(100%_-_16rem)]"
      >
        <div class="z-10 border-b p-2">
          <div v-if="!$md" class="pb-2 text-center text-xl font-semibold">
            Permissions
          </div>
          <TInput
            v-model="search"
            label="Search"
            :error="editor.permissions.error"
            :errorMessage="editor.permissions.errorMessage"
            innerClass="bg-light text-dark !pr-0"
            class="flex-auto"
            @keyup.enter="searchPermissions"
          >
            <template #append>
              <TButton
                icon="search"
                class="h-full rounded-lg bg-primary bg-glossy px-5 text-light"
                :class="[editor.permissions.error && '!bg-negative']"
                @click="searchPermissions"
              />
            </template>
            <template #error><span></span></template>
          </TInput>
          <TButton
            label="None"
            :icon="
              !!editor.permissions.value.find((item) => item == none)
                ? 'check_box'
                : 'check_box_outline_blank'
            "
            @click="selectPermission({ id: none })"
            class="rounded-lg px-2 py-1"
            :disabled="!canEdit"
          />
        </div>
        <div class="flex-auto px-2">
          <div
            class="permissionsWrapper grid items-start gap-2"
            ref="permissionsWrapper"
          >
            <template v-if="!loading && permissions.length <= 0">
              <div
                class="col-span-full text-center font-semibold italic text-gray-400"
              >
                No permissions found!
              </div>
            </template>
            <template v-for="permission in permissions" :key="permission.id">
              <PermissionItem
                :modelValue="permission"
                :active="isSelected(permission)"
                @click="selectPermission(permission)"
                :canEdit="canEdit"
              />
            </template>
          </div>
          <SizeObserver @resize="onWrapperResize" />
        </div>
        <div class="flex items-center justify-center">
          <TPagination
            v-model="pagination.page"
            v-model:limit="pagination.limit"
            v-model:offset="pagination.offset"
            v-model:totalPage="pagination.pages"
            :total="pagination.total"
            @paginate="searchPermissions"
          />
        </div>
      </div>
    </div>
    <TInnerLoading :active="_loading" :text="_loadingMessage" />
  </div>
</template>

<script setup>
import { inject, ref, reactive, onMounted, computed } from "vue";
import { useVModel } from "@vueuse/core";
import { InputField, Helpers } from "@/scripts";
import { useSearcher, useGuard } from "@/plugins/composables";
import PermissionItem from "./permissionItem.vue";

const $api = inject("$api");
const $guard = useGuard();
const $screen = inject("$screen");

const $md = $screen.value.greaterOrEqual("md");

const props = defineProps({
  modelValue: Object,
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
  "update:modelValue",
  "update:loading",
  "update:loadingMessage",
  "back",
]);

const { searcher, pagination } = useSearcher("/roles/permissions");

const search = ref("");
const permissionsWrapper = ref(null);

const permissions = ref([]);

const editor = reactive({
  name: new InputField(props.modelValue?.name)
    .setName("Name")
    .setRules("required"),
  description: new InputField(props.modelValue?.description).setName(
    "Description"
  ),
  permissions: new InputField(props.modelValue?.permissions ?? [])
    .setName("Permissions")
    .setRules("required"),
});

const _loading = useVModel(props, "loading", emit);
const _loadingMessage = useVModel(props, "loadingMessage", emit);

const none = ref(null);
const isEdit = computed(() => !!props.modelValue?.id);
const canEdit = computed(
  () =>
    (isEdit.value && $guard.can(["roles_edit"])) ||
    (!isEdit.value && $guard.can(["roles_add"]))
);

const onWrapperResize = (size) => {
  let cols = Math.floor(size.width / 320);
  permissionsWrapper.value.style.setProperty("--columns", cols);
};

const searchPermissions = () => {
  _loading.value = true;
  _loadingMessage.value = "Fetching permissions, please wait...";

  searcher({ search: search.value })
    .then((response) => {
      pagination.value.total = response.data.count;
      permissions.value = response.data.data;
      none.value = response.data.none;
      if (!props.modelValue?.id) {
        editor.permissions.value = [response.data.none];
      }
    })
    .finally(() => {
      _loading.value = false;
    });
};

const saveRole = () => {
  if (validate()) {
    _loading.value = true;
    _loadingMessage.value = "Saving Role, please wait...";
    let method = isEdit.value ? "patch" : "post";
    let uri = `/roles${isEdit.value ? "/" + props.modelValue.id : ""}`;
    let data = {
      permissions: editor.permissions.value,
    };
    if (!props.modelValue?.protected) {
      data = Object.assign({}, data, {
        name: editor.name.value,
        description: editor.description.value,
      });
    }
    $api[method](uri, data)
      .then((response) => {
        emit("update:modelValue", response.data.data);
      })
      .catch((error) => {
        Helpers.onRequestError(error, editor);
      })
      .finally(() => {
        _loading.value = false;
      });
  }
};

const selectPermission = (permission) => {
  if (canEdit.value) {
    if (permission.id == none.value) {
      editor.permissions.value = [none.value];
    } else {
      editor.permissions.value = editor.permissions.value.filter(
        (item) => item != none.value
      );

      if (isSelected(permission)) {
        editor.permissions.value = editor.permissions.value.filter(
          (item) => item != permission.id
        );
        if (
          permissions.id != none.value &&
          editor.permissions.value.length <= 0
        ) {
          editor.permissions.value = [none.value];
        }
      } else {
        editor.permissions.value.push(permission.id);
      }
    }
  }
};

const isSelected = (permission) => {
  return !!editor.permissions.value.find((item) => item == permission.id);
};

const validate = () => {
  if (!props.modelValue?.protected) {
    editor.name.validate();
    editor.description.validate();
  }
  editor.permissions.validate();
  return !(
    editor.name.error ||
    editor.description.eror ||
    editor.permissions.error
  );
};

onMounted(() => {
  searchPermissions();
});
</script>

<style scoped lang="scss">
.permissionsWrapper {
  grid-template-columns: repeat(var(--columns), 1fr);
}
</style>
