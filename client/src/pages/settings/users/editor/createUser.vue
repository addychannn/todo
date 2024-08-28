<template>
  <TCard
    class="relative max-h-[95%] w-[95dvw] max-w-sm bg-background-accent text-foreground"
  >
    <TCardHeader class="bg-page-background">
      <TCardTitle> Create User </TCardTitle>
      <TButton
        icon="close"
        iconSize="sm"
        class="aspect-square rounded-full p-0.5"
        @click="emit('close')"
      />
    </TCardHeader>
    <div class="px-3 py-1 text-center font-semibold uppercase">
      {{ formPage[currentPage].title }}
    </div>
    <TCardBody
      class="relative min-h-[12rem]"
      :class="[transitioning && '!overflow-hidden']"
    >
      <transition
        v-bind="transitions[transitionToRight ? 'toRight' : 'toLeft']"
        @before-leave="transitioning = true"
        @after-enter="transitioning = false"
      >
        <div v-if="currentPage == 0" class="transition-all">
          <TInput
            :label="editor.username.name"
            v-model="editor.username.value"
            :error="editor.username.error"
            :errorMessage="editor.username.errorMessage"
            innerClass="bg-light text-dark"
            @keyup.enter="nextPage"
          />
          <TInput
            :label="editor.email.name"
            v-model="editor.email.value"
            :error="editor.email.error"
            :errorMessage="editor.email.errorMessage"
            innerClass="bg-light text-dark"
            @keyup.enter="nextPage"
          />
          <TInput
            :label="editor.password.name"
            v-model="editor.password.value"
            :error="editor.password.error"
            :errorMessage="editor.password.errorMessage"
            innerClass="bg-light text-dark"
            @keyup.enter="nextPage"
          >
            <template #after>
              <TButton
                icon="loop"
                class="rounded-xl p-1"
                @click="generatePassword"
              />
            </template>
          </TInput>
        </div>
        <div v-else-if="currentPage == 1" class="grid gap-1">
          <div
            v-for="role in roles"
            :key="role.id"
            class="relative flex cursor-pointer select-none items-center rounded-lg border border-foreground/25 px-3 py-1 transition hover:scale-[1.01]"
            :class="[isRoleSelected(role) && 'bg-primary bg-glossy text-light']"
            role="checkbox"
            :aria-checked="isRoleSelected(role)"
            @click="selectRole(role)"
          >
            <FocusHelper color="bg-foreground" />
            <div class="flex-auto">
              {{ role.name }}
            </div>
            <TIcon v-if="isRoleSelected(role)" name="check_circle" size="sm" />
          </div>
        </div>
      </transition>
    </TCardBody>
    <div v-if="editor.roles.error" class="bg-negative px-3 text-xs text-white">
      {{ editor.roles.errorMessage }}
    </div>
    <TCardFooter class="flex items-center justify-end gap-1">
      <TButton
        v-if="currentPage >= 1"
        label="Prev"
        @click="prevPage"
        class="rounded-full px-3 py-1"
      />
      <TButton
        v-if="currentPage < formPage.length - 1"
        label="Next"
        @click="nextPage"
        class="rounded-full px-3 py-1"
      />
      <TButton
        v-if="currentPage == formPage.length - 1"
        label="Save"
        class="rounded-full bg-primary bg-glossy px-3 py-1 text-light"
        @click="addUser"
      />
      <TButton
        label="Cancel"
        class="rounded-full px-3 py-1"
        @click="emit('close')"
      />
    </TCardFooter>
    <TInnerLoading :active="loading" :text="loadingMessage" />
  </TCard>
</template>

<script setup>
import { inject, onMounted, reactive, ref } from "vue";
import { InputField, Helpers, transitions } from "@/scripts";

const $api = inject("$api");
const props = defineProps({});

const emit = defineEmits(["created", "close"]);

const roles = ref([]);
const loading = ref(false);
const loadingMessage = ref("");
const currentPage = ref(0);
const formPage = ref([
  {
    title: "Account info",
    valid: () => validate(),
  },
  {
    title: "Roles",
    valid: () => validateRoles(),
  },
]);
const transitioning = ref(false);
const transitionToRight = ref(false);

const editor = reactive({
  username: new InputField().setName("Username").setRules("required|username"),
  email: new InputField().setName("Email").setRules("email"),
  password: new InputField().setName("Password").setRules("required|password"),
  roles: new InputField([]).setName("Roles").setRules("required"),
});

const nextPage = () => {
  changePage(currentPage.value + 1);
};

const prevPage = () => {
  changePage(currentPage.value - 1);
};

const changePage = (page) => {
  if (page >= formPage.value.length) {
    currentPage.value = formPage.value.length - 1;
  } else if (
    currentPage.value < page &&
    formPage.value[currentPage.value].valid()
  ) {
    transitionToRight.value = true;
    currentPage.value = page;
  } else if (currentPage.value > page) {
    transitionToRight.value = false;
    currentPage.value = page;
  }
};

const loadRoles = () => {
  loading.value = true;
  loadingMessage.value = "Preparing editor, please wait...";

  $api
    .get("/users/roles")
    .then((response) => {
      roles.value = response.data.data;
    })
    .finally(() => {
      loading.value = false;
    });
};

const generatePassword = async () => {
  let iterations = 7;

  for (let i = 0; i < iterations; i++) {
    editor.password.value = Helpers.passwordGeneratorFn(8);
    await new Promise((resolve) => setTimeout(resolve, 50));
  }
};

const selectRole = (role) => {
  if (isRoleSelected(role)) {
    editor.roles.value = editor.roles.value.filter(
      (item) => item.id != role.id
    );
  } else {
    editor.roles.value.push(role);
  }
};

const isRoleSelected = (role) => {
  return !!editor.roles.value.find((item) => item.id == role.id);
};

const addUser = () => {
  if (validate() && validateRoles()) {
    loading.value = true;
    loadingMessage.value = "Creating account, please wait...";

    $api
      .post(`/users`, {
        username: editor.username.value,
        email: editor.email.value,
        password: editor.password.value,
        roles: editor.roles.value.map((role) => role.id),
      })
      .then((response) => {
        emit("created", response.data.data);
        notify({
          title: "Success!",
          type: "positive",
          text: response.data.message,
        });
      })
      .catch((error) => {
        Helpers.onRequestError(error, editor);
        if (
          editor.username.error ||
          editor.email.erro ||
          editor.password.error
        ) {
          changePage(0);
        }
      })
      .finally(() => {
        loading.value = false;
      });
  }
};

const validate = () => {
  editor.username.validate();
  editor.email.validate();
  editor.password.validate();

  return !(
    editor.username.error ||
    editor.email.error ||
    editor.password.error
  );
};

const validateRoles = () => {
  editor.roles.validate();
  return !editor.roles.error;
};

onMounted(() => loadRoles());
</script>
